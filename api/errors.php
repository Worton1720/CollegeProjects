<?php
/**
 * Обработка ошибок и форматирование JSON ответов
 * Централизованная обработка ошибок, логирование и генерация JSON ответов
 */

class ErrorHandler {
    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_NOT_FOUND = 404;
    const HTTP_SERVER_ERROR = 500;

    private static $requestStartTime = null;

    /**
     * Инициализация логирования запросов
     */
    public static function initRequest() {
        self::$requestStartTime = microtime(true);

        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

        $requestLog = sprintf(
            "[%s] %s %s from %s",
            date('Y-m-d H:i:s'),
            $method,
            $uri,
            $ip
        );

        self::logToFile($requestLog, 'requests');

        // Логирование тела запроса для POST/PUT
        if ($method === 'POST' || $method === 'PUT') {
            $body = file_get_contents('php://input');
            if ($body) {
                self::logToFile("  Body: " . substr($body, 0, 500), 'requests');
            }
        }
    }

    /**
     * Отправить успешный JSON ответ
     */
    public static function success($data = null, $code = 200) {
        self::logResponse($code, 'SUCCESS');

        http_response_code($code);
        header('Content-Type: application/json');

        $response = [
            'status' => 'success',
            'data' => $data,
            'timestamp' => date('c')
        ];

        echo json_encode($response);
        exit;
    }

    /**
     * Отправить JSON ответ об ошибке
     */
    public static function error($message, $code = 400) {
        self::logResponse($code, 'ERROR: ' . $message);

        http_response_code($code);
        header('Content-Type: application/json');

        $response = [
            'status' => 'error',
            'error' => $message,
            'code' => $code,
            'timestamp' => date('c')
        ];

        echo json_encode($response);
        exit;
    }

    /**
     * Обработка ошибок валидации с сообщениями для каждого поля
     */
    public static function validationError($errors = []) {
        self::logResponse(400, 'VALIDATION ERROR: ' . json_encode($errors));

        http_response_code(400);
        header('Content-Type: application/json');

        $response = [
            'status' => 'error',
            'error' => 'Validation failed',
            'errors' => $errors,
            'code' => 400,
            'timestamp' => date('c')
        ];

        echo json_encode($response);
        exit;
    }

    /**
     * Логирование ответа с временем выполнения
     */
    private static function logResponse($code, $message) {
        $duration = microtime(true) - self::$requestStartTime;
        $duration_ms = round($duration * 1000, 2);

        $responseLog = sprintf(
            "  Response: HTTP %d | %s | %sms",
            $code,
            $message,
            $duration_ms
        );

        self::logToFile($responseLog, 'requests');
    }

    /**
     * Логирование запроса к базе данных
     */
    public static function logQuery($sql, $params = [], $duration_ms = 0) {
        $queryLog = sprintf(
            "[%s] SQL: %s | Params: %s | Duration: %sms",
            date('Y-m-d H:i:s'),
            substr($sql, 0, 200),
            json_encode($params),
            $duration_ms
        );

        self::logToFile($queryLog, 'queries');
    }

    /**
     * Логирование операции (универсальное логирование)
     */
    public static function logOperation($entity, $action, $details = '') {
        $operationLog = sprintf(
            "[%s] %s.%s | %s",
            date('Y-m-d H:i:s'),
            $entity,
            $action,
            $details
        );

        self::logToFile($operationLog, 'operations');
    }

    /**
     * Логирование в специфический файл в директории logs
     */
    private static function logToFile($message, $type = 'error') {
        $logDir = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }

        $logFile = $logDir . '/' . $type . '.log';
        file_put_contents($logFile, $message . "\n", FILE_APPEND);
    }

    /**
     * Логирование ошибки (универсальное, для предупреждений и исключений)
     */
    public static function log($message, $level = 'ERROR') {
        $logMessage = sprintf(
            "[%s] [%s] %s",
            date('Y-m-d H:i:s'),
            $level,
            $message
        );

        self::logToFile($logMessage, 'error');
    }

    /**
     * Установка глобального обработчика ошибок
     */
    public static function setupGlobalHandler() {
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            $errorLog = "PHP Error in $errfile:$errline - $errstr (errno: $errno)";
            self::log($errorLog, 'WARNING');
        });

        set_exception_handler(function ($exception) {
            $exceptionLog = sprintf(
                "Exception: %s in %s:%d\nTrace: %s",
                $exception->getMessage(),
                $exception->getFile(),
                $exception->getLine(),
                $exception->getTraceAsString()
            );
            self::log($exceptionLog, 'EXCEPTION');
            self::error('Internal server error', 500);
        });
    }
}

// Инициализация логирования запросов
ErrorHandler::initRequest();

// Установка глобальной обработки ошибок
ErrorHandler::setupGlobalHandler();

// Включение отображения ошибок для разработки
error_reporting(E_ALL);
ini_set('display_errors', 0);
?>
