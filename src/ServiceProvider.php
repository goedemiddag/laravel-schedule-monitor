<?php

namespace Goedemiddag\ScheduleMonitor;

use Illuminate\Console\Scheduling\Event;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot(): void
    {
        Event::macro('monitor', function (JobReporter $reporter): Event {
            /** @var Event $event */
            $event = $this;

            if (!$reporter->shouldReport()) {
                return $event;
            }

            return $event
                ->before($reporter->inProgress(...))
                ->onSuccess($reporter->success(...))
                ->onFailure($reporter->failed(...));
        });

        Event::macro('monitorWithSentry', function (?string $uuid): Event {
            /** @var Event $event */
            $event = $this;

            /* @phpstan-ignore-next-line */
            return $event->monitor(new SentryReporter($uuid));
        });
    }
}
