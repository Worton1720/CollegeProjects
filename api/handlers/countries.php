<?php
/**
 * Обработчик стран
 * Управляет CRUD операциями для стран
 */

class CountriesHandler {
    private static $db = null;

    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }

    /**
     * GET все страны
     */
    public static function getAll() {
        try {
            $db = self::getDb();
            $countries = $db->fetchAll('SELECT * FROM countries ORDER BY name ASC');
            ErrorHandler::success($countries, 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error fetching countries: " . $e->getMessage());
            ErrorHandler::error("Failed to fetch countries", 500);
        }
    }

    /**
     * POST создание новой страны
     */
    public static function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = [];
            if (empty($data['name'])) {
                $errors['name'] = 'Country name is required';
            } elseif (strlen($data['name']) > 100) {
                $errors['name'] = 'Country name must not exceed 100 characters';
            }

            if (!isset($data['visa_required'])) {
                $errors['visa_required'] = 'Visa requirement is required';
            }

            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            $db = self::getDb();

            // Проверка на дубликаты
            $exists = $db->fetchOne(
                'SELECT id FROM countries WHERE name = ?',
                [$data['name']]
            );

            if ($exists) {
                ErrorHandler::error('Country with this name already exists', 400);
            }

            // Вставка
            $visa = $data['visa_required'] ? 1 : 0;
            $db->execute(
                'INSERT INTO countries (name, visa_required) VALUES (?, ?)',
                [$data['name'], $visa]
            );

            $id = $db->lastInsertId();
            $country = $db->fetchOne('SELECT * FROM countries WHERE id = ?', [$id]);

            ErrorHandler::success($country, 201);
        } catch (Exception $e) {
            ErrorHandler::log("Error creating country: " . $e->getMessage());
            ErrorHandler::error("Failed to create country", 500);
        }
    }

    /**
     * PUT обновление страны
     */
    public static function update($id) {
        try {
            if (empty($id)) {
                ErrorHandler::error("Country ID is required", 400);
            }

            $db = self::getDb();

            // Проверка существования страны
            $existing = $db->fetchOne('SELECT * FROM countries WHERE id = ?', [$id]);
            if (!$existing) {
                ErrorHandler::error("Country not found", 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = [];
            if (empty($data['name'])) {
                $errors['name'] = 'Country name is required';
            } elseif (strlen($data['name']) > 100) {
                $errors['name'] = 'Country name must not exceed 100 characters';
            }

            if (!isset($data['visa_required'])) {
                $errors['visa_required'] = 'Visa requirement is required';
            }

            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            // Проверка на дубликат (исключая текущий)
            $duplicate = $db->fetchOne(
                'SELECT id FROM countries WHERE name = ? AND id != ?',
                [$data['name'], $id]
            );

            if ($duplicate) {
                ErrorHandler::error('Country with this name already exists', 400);
            }

            // Обновление
            $visa = $data['visa_required'] ? 1 : 0;
            $db->execute(
                'UPDATE countries SET name = ?, visa_required = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
                [$data['name'], $visa, $id]
            );

            $country = $db->fetchOne('SELECT * FROM countries WHERE id = ?', [$id]);
            ErrorHandler::success($country, 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error updating country: " . $e->getMessage());
            ErrorHandler::error("Failed to update country", 500);
        }
    }

    /**
     * DELETE удаление страны
     */
    public static function delete($id) {
        try {
            if (empty($id)) {
                ErrorHandler::error("Country ID is required", 400);
            }

            $db = self::getDb();

            // Проверка существования страны
            $existing = $db->fetchOne('SELECT * FROM countries WHERE id = ?', [$id]);
            if (!$existing) {
                ErrorHandler::error("Country not found", 404);
            }

            // Проверка наличия назначенных туров
            $tours = $db->fetchOne(
                'SELECT COUNT(*) as count FROM tours WHERE country_id = ?',
                [$id]
            );

            if ($tours['count'] > 0) {
                ErrorHandler::error(
                    'Cannot delete country with assigned tours. Delete all tours for this country first.',
                    400
                );
            }

            // Удаление
            $db->execute('DELETE FROM countries WHERE id = ?', [$id]);

            ErrorHandler::success(['id' => $id, 'deleted' => true], 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error deleting country: " . $e->getMessage());
            ErrorHandler::error("Failed to delete country", 500);
        }
    }
}
?>
