<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Twenty20\MonitorAgent\Jobs\MonitorJob;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Http::preventStrayRequests(); // Prevent any outgoing HTTP requests

    Config::set('monitor-agent.domains', [
        'https://google.com',
        'https://another-site.com'
    ]);
    Config::set('monitor-agent.interval', 60);
});
test('pings configured domains, logs responses, and re-dispatches itself', function () {
    Http::fake([
        'https://example.com' => Http::response('', 200),
        'https://another-site.com' => Http::response('', 500),
    ]);

    // Fake the queue so that the re-dispatch can be captured.
    Queue::fake();

    // Set logging expectations.
    Log::shouldReceive('info')
        ->once()
        ->with('MonitorAgent: Pinged https://example.com - Status: 200');

    Log::shouldReceive('error')
        ->once()
        ->withArgs(fn($message) =>
            str_contains($message, 'MonitorAgent: Failed to ping https://another-site.com')
        );

    // Set config values (if not already set via your TestCase).
    Config::set('monitor-agent.domains', [
        'https://example.com',
        'https://another-site.com'
    ]);
    Config::set('monitor-agent.interval', 60);

    // Dispatch the job. With a synchronous queue driver in the testing environment,
    // the handle() method will be executed immediately.
    dispatch(new MonitorJob());

    // Assert that the job re-dispatched itself with a delay of 60 seconds.
    Queue::assertPushed(MonitorJob::class, function ($job) {
        if (!$job->delay) {
            return false;
        }
        $expectedDelayTimestamp = now()->addSeconds(60)->timestamp;
        // Allow a small tolerance in timing.
        return abs($job->delay->timestamp - $expectedDelayTimestamp) < 2;
    });
});
