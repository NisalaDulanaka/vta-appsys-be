<?php

namespace App\Utils;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AppLogger
{
    private static ?Logger $log = null;

    private static function handleLoggerInstance($level = LEVEL::Warning)
    {
        if (AppLogger::$log === null) {
            AppLogger::$log = new Logger('stack_log');
            AppLogger::$log->pushHandler(new StreamHandler('php://stderr', $level));
        }
    }

    public static function error(string $message)
    {
        AppLogger::handleLoggerInstance(LEVEL::Error);
        AppLogger::$log->error($message);
    }

    public static function debug(array $message)
    {
        AppLogger::handleLoggerInstance(LEVEL::Debug);
        AppLogger::$log->debug(json_encode($message, JSON_PRETTY_PRINT));
    }

    public static function warning(string $message)
    {
        AppLogger::handleLoggerInstance();
        AppLogger::$log->error($message);
    }
}
