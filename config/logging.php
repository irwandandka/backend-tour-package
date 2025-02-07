<?php

use Google\Service\Storage;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    | 
    */

    /*
    |--------------------------------------------------------------------------
    | Log Notes
    |--------------------------------------------------------------------------
    | 
    | Log Driver:
    | - single: Logs will be written to a single log file (e.g., `laravel.log`).
    |   This is the default and does not rotate logs, meaning everything will
    |   be written to the same file indefinitely.
    | - daily: Creates a new log file each day (e.g., `laravel-YYYY-MM-DD.log`).
    |   Useful for log rotation, where logs are split by date and old logs are archived.
    | - slack: Sends log messages to a Slack channel. Requires a Slack webhook URL.
    | - syslog: Logs are sent to the system's syslog, which may be viewed via system 
    |   utilities like `journalctl` on Linux.
    | - errorlog: Logs are sent to the PHP error log, which is typically the system’s 
    |   default error log file (e.g., `/var/log/apache2/error.log`).
    | - custom: Allows you to define your own custom logging behavior using a custom 
    |   log class that implements the `Log` interface.
    | 
    | Log Level:
    | - debug: Logs all messages, including detailed debugging information, 
    |   as well as higher severity levels (info, warning, error, etc.).
    | - info: Logs informational messages, typically for general progress updates.
    | - notice: Logs normal but significant events that are not errors.
    | - warning: Logs potential problems or minor issues that don’t stop execution.
    | - error: Logs errors that occur but don’t prevent the application from running.
    | - critical: Logs critical issues that may cause the application to stop working.
    | - alert: Logs issues requiring immediate action.
    | - emergency: Logs the highest severity, indicating a system-wide failure.
    | 
    | Example:
    | - 'level' => 'debug': Captures all messages, including detailed debug information.
    | - 'level' => 'warning': Only logs warnings, errors, and more critical messages.
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        // log service https://papertrailapp.com/
        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        // will send into server log file eg: /var/log/nginx/error.log
        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'testing' => [
            'driver' => 'single',
            'path' => storage_path('logs/testing.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'system-error' => [
            'driver' => 'single',
            'path' => storage_path('logs/system-error.log'),
            'level' => 'error',
            'replace_placeholders' => true,
        ],

        'crawling' => [
            'driver' => 'single',
            'path' => storage_path('logs/crawling.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],
    ],

];
