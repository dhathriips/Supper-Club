<?php
// Local XAMPP defaults. Change these values if your local database uses another account.
return [
    'dsn' => getenv('SUPPER_DSN') ?: 'mysql:host=127.0.0.1;port=3306;dbname=supper_club;charset=utf8mb4',
    'user' => getenv('SUPPER_DB_USER') ?: 'root',
    'password' => getenv('SUPPER_DB_PASSWORD') ?: '',
];
