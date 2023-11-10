<?php

namespace Goedemiddag\ScheduleMonitor\Exceptions;

use RuntimeException;

class DependencyMissingException extends RuntimeException implements ScheduleMonitoringException
{
}
