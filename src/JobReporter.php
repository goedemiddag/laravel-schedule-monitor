<?php

namespace Goedemiddag\ScheduleMonitor;

interface JobReporter
{
    public function inProgress(): void;


    public function success(): void;


    public function failed(): void;


    public function shouldReport(): bool;
}
