<?php

namespace Goedemiddag\ScheduleMonitor\Tests;

use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\EventMutex;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Mockery as m;

final class SentryTest extends TestCase
{
    public function testSuccessfulJob(): void
    {
        Http::fake();

        $event = new Event(m::mock(EventMutex::class), 'exit 0', 'UTC');

        config(['sentry.dsn' => 'test']);

        $event
            ->monitorWithSentry('foobar')
            ->run(app());

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sentry.io/api/0/monitors/foobar/checkins/'
                && $request->method() === 'POST'
                && $request->header('Authorization') === ['DSN test']
                && $request->body() === '{"status":"in_progress"}';
        });

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sentry.io/api/0/monitors/foobar/checkins/latest'
                && $request->method() === 'PUT'
                && $request->header('Authorization') === ['DSN test']
                && $request->body() === '{"status":"ok"}';
        });
    }

    public function testUnsuccessfulJob(): void
    {
        Http::fake();

        $event = new Event(m::mock(EventMutex::class), 'exit 1', 'UTC');

        config(['sentry.dsn' => 'test']);

        $event
            ->monitorWithSentry('foobar')
            ->run(app());

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sentry.io/api/0/monitors/foobar/checkins/'
                && $request->method() === 'POST'
                && $request->header('Authorization') === ['DSN test']
                && $request->body() === '{"status":"in_progress"}';
        });

        Http::assertSent(function (Request $request) {
            return $request->url() === 'https://sentry.io/api/0/monitors/foobar/checkins/latest'
                && $request->method() === 'PUT'
                && $request->header('Authorization') === ['DSN test']
                && $request->body() === '{"status":"error"}';
        });
    }
}
