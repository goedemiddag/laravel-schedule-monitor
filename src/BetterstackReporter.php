<?php

namespace Goedemiddag\ScheduleMonitor;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class BetterstackReporter implements JobReporter
{
    public function __construct(
        protected readonly ?string $heartbeatId = null,
    ) {
    }

    private function http(): PendingRequest
    {
        $id = $this->heartbeatId;

        assert(is_string($id), 'ID not provided');

        return Http::baseUrl("https://uptime.betterstack.com/api/v1/heartbeat/{$id}");
    }

    public function shouldReport(): bool
    {
        return isset($this->heartbeatId);
    }

    public function inProgress(): void
    {
        // Not supported by Better Stack
    }

    public function success(): void
    {
        $this->http()->post('');
    }

    public function failed(): void
    {
        $this->http()->post('fail');
    }
}
