<?php

use Spatie\Activitylog\Models\Activity;

return [
    'default_log_name'     => 'default',
    'default_auth_driver'  => null,
    'activity_model'       => Activity::class,
    'table_name'           => 'activity_log',
    'database_connection'  => env('ACTIVITY_LOG_DB_CONNECTION'),
    'clean_logs_older_than_days' => 365,

    'delete_records_older_than_days' => 365,

    'subject_returns_soft_deleted_models' => false,

    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),
];
