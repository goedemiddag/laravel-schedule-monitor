<?php

namespace Goedemiddag\ScheduleMonitor\Tests;

use Goedemiddag\ScheduleMonitor\BetterstackReporter;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\EventMutex;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery as m;

final class BetterstackTest extends TestCase
{
    public function test_shouldnt_report_without_monitor_id(): void
    {
        $monitor = new BetterstackReporter(null);

        $this->assertFalse($monitor->shouldReport());
    }

    public function test_successful_job(): void
    {
        Http::fake();

        $event = new Event(m::mock(EventMutex::class), 'exit 0', 'UTC');

        $event
            ->monitorWithBetterstack('foobar')
            ->run(app());

        Http::assertSentInOrder([
            function (Request $request): bool {
                return $request->url() === 'https://uptime.betterstack.com/api/v1/heartbeat/foobar/'
                    && $request->method() === 'POST';
            },
        ]);
    }

    public function test_unsuccessful_job(): void
    {
        Http::fake();

        $event = new Event(m::mock(EventMutex::class), 'exit 1', 'UTC');

        $event
            ->monitorWithBetterstack('foobar')
            ->run(app());

        Http::assertSentInOrder([
            function (Request $request): bool {
                return $request->url() === 'https://uptime.betterstack.com/api/v1/heartbeat/foobar/fail'
                    && $request->method() === 'POST';
            },
        ]);
    }
}
