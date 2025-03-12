<?php

namespace Twenty20\MonitorAgent;

use Spatie\LaravelPackageTools\Package;
use Twenty20\MonitorAgent\Commands\InstallCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MonitorAgentServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('monitor-agent')
            ->hasConfigFile('monitor-agent')
            ->hasCommand(InstallCommand::class);
    }

    public function boot()
    {
        parent::boot();

        // publish the config file
        $this->publishes([
            __DIR__.'/../config/monitor-agent.php' => config_path('monitor-agent.php'),
        ], 'monitor-agent-config');

        $this->publishes([
            __DIR__.'/Jobs/MonitorJob.php' => app_path('Jobs/MonitorJob.php'),
        ], 'monitor-agent-jobs');

    }
}
