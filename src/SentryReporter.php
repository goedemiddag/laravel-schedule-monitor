<?php

namespace Goedemiddag\ScheduleMonitor;

use Closure;
use Goedemiddag\ScheduleMonitor\Exceptions\DependencyMissingException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Sentry\Dsn;

class SentryReporter implements JobReporter
{
    protected static ?Closure $resolveDsnCallback = null;

    public function __construct(
        protected readonly ?string $monitorId,
        protected readonly ?string $dsn = null,
    ) {
        if (!class_exists(Dsn::class)) {
            throw new DependencyMissingException('Sentry\\Dsn not found. Install sentry/sentry or sentry/sentry-laravel to use this driver.');
        }
    }

    private function http(): PendingRequest
    {
        $dsnString = $this->resolveDsn();

        assert(is_string($dsnString), 'DSN not provided');

        $dsn = Dsn::createFromString($dsnString);

        $url = $dsn->getScheme() . '://';

        // @see https://github.com/getsentry/develop/blob/06254d2510d16296367c8302099a40f14863193f/src/components/codeContext.tsx#L92-L96
        $url .= str_contains($dsn->getHost(), '.ingest.')
            ? explode('.ingest.', $dsn->getHost())[1] // SaaS
            : $dsn->getHost(); // Self hosted

        if (($dsn->getScheme() === 'http' && $dsn->getPort() !== 80)
            || ($dsn->getScheme() === 'https' && $dsn->getPort() !== 443)) {
            $url .= ':' . $dsn->getPort();
        }

        $url .= $dsn->getPath();

        return Http::baseUrl("{$url}/api/0/monitors/{$this->monitorId}/checkins")
            ->withToken($dsnString, 'DSN');
    }

    public static function resolveDsnUsing(?Closure $callback): void
    {
        self::$resolveDsnCallback = $callback;
    }

    private function resolveDsn(): mixed
    {
        if (isset($this->dsn)) {
            return $this->dsn;
        }

        if (isset(self::$resolveDsnCallback)) {
            return call_user_func(self::$resolveDsnCallback);
        }

        return config('sentry.dsn');
    }

    public function shouldReport(): bool
    {
        $dsn = $this->resolveDsn();

        return isset($dsn, $this->monitorId);
    }

    public function inProgress(): void
    {
        $this->http()->post('/', [
            'status' => 'in_progress',
        ]);
    }

    public function success(): void
    {
        $this->http()->put('latest/', [
            'status' => 'ok',
        ]);
    }

    public function failed(): void
    {
        $this->http()->put('latest/', [
            'status' => 'error',
        ]);
    }
}
