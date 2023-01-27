# Laravel schedule monitor

This package allows you to monitor your scheduled commands and jobs.

Supported implementations:
- [Sentry](https://docs.sentry.io/product/crons/)

## Installation

First use composer to install the package using the following command

```sh
composer require goedemiddag/laravel-schedule-monitor
```

## Usage

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
