<?php
function getPdo(): PDO
{
    $host     = 'db';      // MySQLコンテナのサービス名
    $dbname   = 'posse';   // MYSQL_DATABASE
    $user     = 'root';    // rootユーザー
    $password = 'root';    // rootパスワード

    $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    return new PDO($dsn, $user, $password, $options);
}
