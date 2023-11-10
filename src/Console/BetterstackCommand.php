<?php

namespace Goedemiddag\ScheduleMonitor\Console;

use Goedemiddag\ScheduleMonitor\BetterstackReporter;
use Illuminate\Console\Command;

class BetterstackCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'schedule:monitor:betterstack {id}';

    /**
     * @var string
     */
    protected $description = 'Dispatch a signal to Betterstack cron monitoring';

    public function handle(): int
    {
        assert(is_string($this->argument('id')), 'ID must be a string');

        $reporter = new BetterstackReporter($this->argument('id'));

        if (!$reporter->shouldReport()) {
            $this->error("Can't report: missing Heartbeat ID");

            return self::FAILURE;
        }

        $reporter->success();

        return self::SUCCESS;
    }
}
