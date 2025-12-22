<?php
/**
 * Инициализация базы данных
 * Создаёт базу данных и таблицы при первом API запросе, если они не существуют
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/errors.php';

class DatabaseInitializer {
    public static function initialize() {
        try {
            $db = Database::getInstance();
            $pdo = $db->connect();

            // Создание таблиц, если они не существуют
            self::createAdminsTable($pdo);
            self::createCountriesTable($pdo);
            self::createClientsTable($pdo);
            self::createToursTable($pdo);

            // Создание администратора по умолчанию, если его нет
            self::createDefaultAdmin($pdo);

            // Добавление стран по умолчанию, если они отсутствуют
            self::seedDefaultCountries($pdo);

            return true;
        } catch (Exception $e) {
            ErrorHandler::log("Database initialization failed: " . $e->getMessage());
            throw $e;
        }
    }

    private static function createAdminsTable($pdo) {
        $sql = "
            CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                last_login DATETIME
            )
        ";
        $pdo->exec($sql);
    }

    private static function createDefaultAdmin($pdo) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM admins");
            $result = $stmt->fetch();

            if ($result['count'] == 0) {
                // Администратор по умолчанию: username=admin, password=Admin@12345
                $username = 'admin';
                $password = password_hash('Admin@12345', PASSWORD_BCRYPT, ['cost' => 12]);

                $stmt = $pdo->prepare('INSERT INTO admins (username, password) VALUES (?, ?)');
                $stmt->execute([$username, $password]);

                ErrorHandler::log("Default admin created: username='admin', password='Admin@12345'", 'INFO');
            }
        } catch (Exception $e) {
            // Игнорирование ошибок при создании администратора по умолчанию
            ErrorHandler::log("Could not create default admin: " . $e->getMessage(), 'WARNING');
        }
    }

    private static function seedDefaultCountries($pdo) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM countries");
            $result = $stmt->fetch();

            if ($result['count'] == 0) {
                // Список стран по умолчанию
                $countries = [
                    ['name' => 'Россия', 'visa_required' => 0],
                    ['name' => 'Египет', 'visa_required' => 1],
                    ['name' => 'Испания', 'visa_required' => 0],
                    ['name' => 'Италия', 'visa_required' => 0],
                    ['name' => 'Франция', 'visa_required' => 0],
                    ['name' => 'Таиланд', 'visa_required' => 1],
                    ['name' => 'Турция', 'visa_required' => 1],
                    ['name' => 'Австрия', 'visa_required' => 0],
                    ['name' => 'Чехия', 'visa_required' => 0],
                    ['name' => 'Греция', 'visa_required' => 0]
                ];

                $stmt = $pdo->prepare('INSERT INTO countries (name, visa_required) VALUES (?, ?)');

                foreach ($countries as $country) {
                    $stmt->execute([$country['name'], $country['visa_required']]);
                }

                ErrorHandler::log("Default countries created: " . count($countries) . " countries added", 'INFO');
            }
        } catch (Exception $e) {
            // Игнорирование ошибок при заполнении стран по умолчанию
            ErrorHandler::log("Could not seed countries: " . $e->getMessage(), 'WARNING');
        }
    }

    private static function createCountriesTable($pdo) {
        $sql = "
            CREATE TABLE IF NOT EXISTS countries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                visa_required BOOLEAN NOT NULL DEFAULT 0,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $pdo->exec($sql);
    }

    private static function createClientsTable($pdo) {
        $sql = "
            CREATE TABLE IF NOT EXISTS clients (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                full_name TEXT NOT NULL,
                phone TEXT NOT NULL,
                passport_data TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ";
        $pdo->exec($sql);
    }

    private static function createToursTable($pdo) {
        $sql = "
            CREATE TABLE IF NOT EXISTS tours (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                title TEXT NOT NULL,
                country_id INTEGER NOT NULL,
                start_date TEXT NOT NULL,
                price REAL NOT NULL CHECK(price > 0),
                client_id INTEGER,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (country_id) REFERENCES countries(id),
                FOREIGN KEY (client_id) REFERENCES clients(id)
            )
        ";
        $pdo->exec($sql);
    }
}

// Инициализация базы данных при первом запросе
DatabaseInitializer::initialize();
?>
