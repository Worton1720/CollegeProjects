<?php

declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

const ROLE_ADMIN   = 1;
const ROLE_MANAGER = 2;
const ROLE_CLIENT  = 3;

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_guest(): bool
{
    return current_user() === null;
}

function role_id(): int
{
    $u = current_user();
    return $u ? (int)$u['id_role'] : 0;
}

function is_admin(): bool
{
    return role_id() === ROLE_ADMIN;
}

function can_manage(): bool
{
    return in_array(role_id(), [ROLE_ADMIN, ROLE_MANAGER], true);
}

function role_name(): string
{
    return [
        ROLE_ADMIN   => 'Администратор',
        ROLE_MANAGER => 'Менеджер',
        ROLE_CLIENT  => 'Клиент',
    ][role_id()] ?? 'Гость';
}

function require_admin(): void
{
    if (!is_admin()) {
        http_response_code(403);
        exit('Доступ запрещён: требуется роль администратора.');
    }
}

function require_manager(): void
{
    if (!can_manage()) {
        http_response_code(403);
        exit('Доступ запрещён: требуется роль менеджера или администратора.');
    }
}

function try_login(string $login, string $password): bool
{
    $st = db()->prepare('SELECT id, id_role, fio, login FROM sotrudnik WHERE login = :l AND password = :p');
    $st->execute([':l' => $login, ':p' => $password]);
    $user = $st->fetch();
    if ($user) {
        $_SESSION['user'] = $user;
        return true;
    }
    return false;
}

function logout(): void
{
    $_SESSION = [];
    session_destroy();
}

function flash(string $type, string $text): void
{
    $_SESSION['flash'] = ['type' => $type, 'text' => $text];
}
