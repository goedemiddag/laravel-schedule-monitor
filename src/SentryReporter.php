<?php

namespace Goedemiddag\ScheduleMonitor;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class SentryReporter implements JobReporter
{
    protected readonly string $dsn;


    public function __construct(
        protected readonly ?string $monitorId,
    ) {
        $dsn = config('sentry.dsn');

        if (is_string($dsn) && !empty($dsn)) {
            $this->dsn = $dsn;
        }
    }


    private function http(): PendingRequest
    {
        return Http::baseUrl("https://sentry.io/api/0/monitors/{$this->monitorId}/checkins")
            ->withToken($this->dsn, 'DSN');
    }


    public function shouldReport(): bool
    {
        return isset($this->dsn, $this->monitorId);
    }


    public function inProgress(): void
    {
        $this->http()->post('', [
            'status' => 'in_progress',
        ]);
    }


    public function success(): void
    {
        $this->http()->put('latest', [
            'status' => 'ok',
        ]);
    }


    public function failed(): void
    {
        $this->http()->put('latest', [
            'status' => 'error',
        ]);
    }
}
