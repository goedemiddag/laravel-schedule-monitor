<?php

namespace Goedemiddag\ScheduleMonitor\Tests;

use Goedemiddag\ScheduleMonitor\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    /**
     * Load the package service provider. This makes sure that, for example the config files are available.
     */
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }
}
