<?php

namespace Twenty20\MonitorAgent\Tests;

use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Twenty20\MonitorAgent\MonitorAgentServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        // Factory::guessFactoryNamesUsing(
        //     fn (string $modelName) => 'Twenty20\\MonitorAgent\\Database\\Factories\\'.class_basename($modelName).'Factory'
        // );
    }

    protected function getPackageProviders($app)
    {
        return [
            MonitorAgentServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app)
    {
        // Set default config for tests
        $app['config']->set('monitor-agent.domains', [
            'https://example.com',
            'https://another-site.com'
        ]);
        $app['config']->set('monitor-agent.interval', 60);
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        /*
         foreach (\Illuminate\Support\Facades\File::allFiles(__DIR__ . '/database/migrations') as $migration) {
            (include $migration->getRealPath())->up();
         }
         */
    }
}
