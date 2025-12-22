<?php
/**
 * Главный маршрутизатор API
 * Распределяет запросы на основе параметров entity и action
 */

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Accept');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/init.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/errors.php';
require_once __DIR__ . '/handlers/auth.php';

// Получение параметров запроса
$entity = $_GET['entity'] ?? null;
$action = $_GET['action'] ?? 'list';

// Проверка параметра entity
if (!$entity) {
    ErrorHandler::error('Missing required parameter: entity', 400);
}

// Endpoints аутентификации не требуют проверки
$publicEndpoints = ['auth'];

// Проверка аутентификации для защищённых endpoints
if (!in_array($entity, $publicEndpoints)) {
    if (!AuthHandler::isAuthenticated()) {
        ErrorHandler::error('Unauthorized - please login', 401);
    }
}

// Загрузка соответствующего обработчика на основе entity
$handler = __DIR__ . '/handlers/' . $entity . '.php';
if (!file_exists($handler)) {
    ErrorHandler::error("Unknown entity: $entity", 404);
}

require_once $handler;

// Маршрутизация на функцию обработчика
switch ($entity) {
    case 'auth':
        handleAuth($action);
        break;
    case 'countries':
        handleCountries($action);
        break;
    case 'clients':
        handleClients($action);
        break;
    case 'tours':
        handleTours($action);
        break;
    default:
        ErrorHandler::error("Unknown entity: $entity", 404);
}

function handleCountries($action) {
    switch ($action) {
        case 'list':
            CountriesHandler::getAll();
            break;
        case 'create':
            CountriesHandler::create();
            break;
        case 'update':
            CountriesHandler::update($_GET['id'] ?? null);
            break;
        case 'delete':
            CountriesHandler::delete($_GET['id'] ?? null);
            break;
        default:
            ErrorHandler::error("Unknown action: $action", 400);
    }
}

function handleClients($action) {
    switch ($action) {
        case 'list':
            ClientsHandler::getAll();
            break;
        case 'create':
            ClientsHandler::create();
            break;
        case 'quick-add':
            ClientsHandler::quickAdd();
            break;
        case 'update':
            ClientsHandler::update($_GET['id'] ?? null);
            break;
        case 'delete':
            ClientsHandler::delete($_GET['id'] ?? null);
            break;
        default:
            ErrorHandler::error("Unknown action: $action", 400);
    }
}

function handleTours($action) {
    switch ($action) {
        case 'list':
            ToursHandler::getAll();
            break;
        case 'create':
            ToursHandler::create();
            break;
        case 'update':
            ToursHandler::update($_GET['id'] ?? null);
            break;
        case 'delete':
            ToursHandler::delete($_GET['id'] ?? null);
            break;
        default:
            ErrorHandler::error("Unknown action: $action", 400);
    }
}
?>
