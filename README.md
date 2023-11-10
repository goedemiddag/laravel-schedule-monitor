# Laravel schedule monitor

This package allows you to monitor your scheduled commands and jobs.

Supported implementations:
- [Better Stack](https://betterstack.com/docs/uptime/cron-and-heartbeat-monitor/)
- [Sentry](https://docs.sentry.io/product/crons/)

## Installation

First use composer to install the package using the following command

```sh
composer require goedemiddag/laravel-schedule-monitor
```

## Usage

### Sentry

Chain the `monitorWithSentry` method onto the schedule. This method accepts the UUID provided
by Sentry.

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command(Inspire::class)
        ->daily()
        ->monitorWithSentry('[uuid]')
}
```

### Better Stack

Chain the `monitorWithBetterstack` method onto the schedule. This method accepts the ID provided
by Better Stack.

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command(Inspire::class)
        ->daily()
        ->monitorWithBetterstack('[id]')
}
```
