<?php

namespace Twenty20\MonitorAgent\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MonitorJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $domains = config('monitor-agent.domains', []);
        $interval = config('monitor-agent.interval', 60);

        if (empty($domains)) {
            Log::warning('MonitorAgent: No domains configured for monitoring.');

            return;
        }

        foreach ($domains as $domain) {
            $this->pingDomain($domain);
        }

        self::dispatch()->delay(now()->addSeconds($interval));
    }

    private function pingDomain($url)
    {
        try {
            $response = Http::get($url);

            if ($response->status() === 200) {
                Log::info("MonitorAgent: Pinged {$url} - Status: {$response->status()}");
            } else {
                Log::error("MonitorAgent: Failed to ping {$url}. Status: {$response->status()}");
            }
        } catch (\Exception $e) {
            Log::error("MonitorAgent: Failed to ping {$url}. Error: ".$e->getMessage());
        }
    }
}
