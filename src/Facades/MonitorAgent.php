<?php

namespace Twenty20\MonitorAgent\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Twenty20\MonitorAgent\MonitorAgent
 */
class MonitorAgent extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Twenty20\MonitorAgent\MonitorAgent::class;
    }
}
