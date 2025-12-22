<?php
/**
 * Обработчик аутентификации
 * Управляет входом, выходом и управлением сессией
 */

session_start();

class AuthHandler {
    private static $db = null;
    private const SESSION_TIMEOUT = 86400; // 24 hours
    private const REMEMBER_TIMEOUT = 2592000; // 30 days

    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }

    /**
     * POST login - аутентификация пользователя
     */
    public static function login() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Проверка входных данных
            if (empty($data['username']) || empty($data['password'])) {
                ErrorHandler::error('Username and password required', 400);
            }

            $db = self::getDb();

            // Поиск администратора по имени пользователя
            $admin = $db->fetchOne(
                'SELECT * FROM admins WHERE username = ?',
                [$data['username']]
            );

            if (!$admin) {
                ErrorHandler::error('Invalid credentials', 401);
            }

            // Проверка пароля
            if (!password_verify($data['password'], $admin['password'])) {
                ErrorHandler::log("Failed login attempt for username: " . $data['username'], 'WARNING');
                ErrorHandler::error('Invalid credentials', 401);
            }

            // Обновление времени последнего входа
            $db->execute(
                'UPDATE admins SET last_login = CURRENT_TIMESTAMP WHERE id = ?',
                [$admin['id']]
            );

            // Создание сессии
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_username'] = $admin['username'];
            $_SESSION['login_time'] = time();

            // Обработка опции "запомнить меня"
            if (!empty($data['remember_me'])) {
                $token = bin2hex(random_bytes(32));
                $expiryTime = time() + self::REMEMBER_TIMEOUT;

                // Установка cookie
                setcookie(
                    'auth_token',
                    $token,
                    $expiryTime,
                    '/',
                    '',
                    isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
                    true
                );

                $_SESSION['remember_token'] = $token;
                $_SESSION['token_expiry'] = $expiryTime;
            }

            ErrorHandler::logOperation('auth', 'login', "Admin '{$admin['username']}' logged in");

            ErrorHandler::success([
                'admin_id' => $admin['id'],
                'username' => $admin['username'],
                'message' => 'Login successful'
            ], 200);

        } catch (Exception $e) {
            ErrorHandler::log("Login error: " . $e->getMessage(), 'ERROR');
            ErrorHandler::error('Login failed', 500);
        }
    }

    /**
     * POST logout - уничтожение сессии
     */
    public static function logout() {
        try {
            // Очистка сессии
            session_destroy();

            // Очистка auth cookie
            setcookie('auth_token', '', time() - 3600, '/', '',
                isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', true);

            ErrorHandler::logOperation('auth', 'logout', "Admin logged out");

            ErrorHandler::success(['message' => 'Logout successful'], 200);
        } catch (Exception $e) {
            ErrorHandler::log("Logout error: " . $e->getMessage(), 'ERROR');
            ErrorHandler::error('Logout failed', 500);
        }
    }

    /**
     * GET check - проверка, аутентифицирован ли пользователь
     */
    public static function check() {
        try {
            self::validateSession();

            ErrorHandler::success([
                'authenticated' => true,
                'admin_id' => $_SESSION['admin_id'],
                'username' => $_SESSION['admin_username']
            ], 200);

        } catch (Exception $e) {
            ErrorHandler::success([
                'authenticated' => false
            ], 200);
        }
    }

    /**
     * Проверка текущей сессии
     * Выбрасывает исключение, если не аутентифицирован
     */
    public static function validateSession() {
        // Проверка, вошёл ли администратор
        if (empty($_SESSION['admin_id'])) {
            throw new Exception('Not authenticated');
        }

        // Проверка тайм-аута сессии
        $currentTime = time();
        $loginTime = $_SESSION['login_time'] ?? 0;

        if ($currentTime - $loginTime > self::SESSION_TIMEOUT) {
            session_destroy();
            throw new Exception('Session expired');
        }

        // Сброс тайм-аута сессии
        $_SESSION['login_time'] = $currentTime;

        // Проверка токена "запомнить меня"
        if (!empty($_SESSION['remember_token'])) {
            $expiryTime = $_SESSION['token_expiry'] ?? 0;
            if ($currentTime > $expiryTime) {
                session_destroy();
                throw new Exception('Remember me token expired');
            }
        }

        return true;
    }

    /**
     * Проверка, является ли запрос от аутентифицированного администратора
     * Используется для защиты API endpoints
     */
    public static function isAuthenticated() {
        try {
            self::validateSession();
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

/**
 * Обработка запросов аутентификации
 * Вызывается из главного маршрутизатора API
 */
function handleAuth($action) {
    switch ($action) {
        case 'login':
            AuthHandler::login();
            break;
        case 'logout':
            AuthHandler::logout();
            break;
        case 'check':
            AuthHandler::check();
            break;
        default:
            ErrorHandler::error("Unknown action: $action", 400);
    }
}
?>
