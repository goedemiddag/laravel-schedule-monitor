<?php

namespace Goedemiddag\ScheduleMonitor\Console;

use Goedemiddag\ScheduleMonitor\SentryReporter;
use Illuminate\Console\Command;

class SentryCommand extends Command
{
    protected $signature = 'schedule:monitor:sentry {uuid} {--dsn=} {--error}';

    protected $description = 'Dispatch a signal to Sentry cron monitoring';

    public function handle(): int
    {
        assert(is_string($this->argument('uuid')), 'UUID must be a string');
        assert(is_string($this->option('dsn')) || is_null($this->option('dsn')), 'DSN must be a string or omitted');

        $reporter = new SentryReporter($this->argument('uuid'), $this->option('dsn'));

        if (!$reporter->shouldReport()) {
            $this->error("Can't report: missing Monitor ID or DSN");
            return self::FAILURE;
        }

        $reporter->inProgress();

        if ($this->option('error')) {
            $reporter->failed();
        } else {
            $reporter->success();
        }

        return self::SUCCESS;
    }
}
