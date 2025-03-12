<?php

namespace Twenty20\MonitorAgent\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\info;

class InstallCommand extends Command
{
    public $signature = 'monitor:install';

    public $description = 'Monitor package for domains';

    public function handle()
    {
        info('Installing....');

        $this->callSilent('vendor:publish', ['--tag' => 'monitor-agent-config']);

        info('Configuration published');

        $this->callSilent('vendor:publish', ['--tag' => 'monitor-agent-jobs']);

        info('Jobs published');

        info('Monitor package installed successfully');
    }
}
