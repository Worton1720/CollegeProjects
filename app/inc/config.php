<?php

declare(strict_types=1);

const APP_NAME = 'МирИгрушек';

const COLOR_MAIN      = '#FFFFFF';
const COLOR_SECONDARY = '#F5DEB3';
const COLOR_ACCENT    = '#DEB887';
const COLOR_DISCOUNT  = '#FFDEAD';
const COLOR_NOSTOCK   = '#ADD8E6';

function db(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $port = getenv('DB_PORT') ?: '5433';
    $name = getenv('DB_NAME') ?: 'root_db';
    $user = getenv('DB_USER') ?: 'db_user';
    $pass = getenv('DB_PASS') ?: 'db_pass';
    $dsn = "pgsql:host=$host;port=$port;dbname=$name;options='--client_encoding=UTF8'";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    return $pdo;
}

function next_id(string $table): int
{
    $row = db()->query("SELECT COALESCE(MAX(id), 0) + 1 AS n FROM $table")->fetch();
    return (int)$row['n'];
}

function e(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
