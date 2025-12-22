<?php
/**
 * Обработчик туров
 * Управляет CRUD операциями для туров с фильтрацией
 */

class ToursHandler {
    private static $db = null;

    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }

    /**
     * GET все туры (с опциональной фильтрацией по стране)
     */
    public static function getAll() {
        try {
            $db = self::getDb();
            $countryId = $_GET['country_id'] ?? null;

            $sql = "
                SELECT t.*, c.name as country_name, cl.full_name as client_name
                FROM tours t
                JOIN countries c ON t.country_id = c.id
                LEFT JOIN clients cl ON t.client_id = cl.id
            ";

            $params = [];
            if ($countryId) {
                $sql .= " WHERE t.country_id = ?";
                $params[] = $countryId;
            }

            $sql .= " ORDER BY t.start_date ASC";

            $tours = $db->fetchAll($sql, $params);
            ErrorHandler::success($tours, 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error fetching tours: " . $e->getMessage());
            ErrorHandler::error("Failed to fetch tours", 500);
        }
    }

    /**
     * POST создание нового тура
     */
    public static function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = self::validateTourData($data);
            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            $db = self::getDb();

            // Проверка существования страны
            $country = $db->fetchOne('SELECT id FROM countries WHERE id = ?', [$data['country_id']]);
            if (!$country) {
                ErrorHandler::error("Selected country does not exist", 400);
            }

            // Проверка клиента, если указан
            if (!empty($data['client_id'])) {
                $client = $db->fetchOne('SELECT id FROM clients WHERE id = ?', [$data['client_id']]);
                if (!$client) {
                    ErrorHandler::error("Selected client does not exist", 400);
                }
            }

            // Вставка
            $clientId = !empty($data['client_id']) ? $data['client_id'] : null;
            $db->execute(
                'INSERT INTO tours (title, country_id, start_date, price, client_id) VALUES (?, ?, ?, ?, ?)',
                [$data['title'], $data['country_id'], $data['start_date'], $data['price'], $clientId]
            );

            $id = $db->lastInsertId();
            $tour = $db->fetchOne(
                'SELECT t.*, c.name as country_name, cl.full_name as client_name FROM tours t
                 JOIN countries c ON t.country_id = c.id
                 LEFT JOIN clients cl ON t.client_id = cl.id
                 WHERE t.id = ?',
                [$id]
            );

            ErrorHandler::success($tour, 201);
        } catch (Exception $e) {
            ErrorHandler::log("Error creating tour: " . $e->getMessage());
            ErrorHandler::error("Failed to create tour", 500);
        }
    }

    /**
     * PUT обновление тура
     */
    public static function update($id) {
        try {
            if (empty($id)) {
                ErrorHandler::error("Tour ID is required", 400);
            }

            $db = self::getDb();

            // Проверка существования тура
            $existing = $db->fetchOne('SELECT * FROM tours WHERE id = ?', [$id]);
            if (!$existing) {
                ErrorHandler::error("Tour not found", 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = self::validateTourData($data);
            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            // Проверка существования страны
            $country = $db->fetchOne('SELECT id FROM countries WHERE id = ?', [$data['country_id']]);
            if (!$country) {
                ErrorHandler::error("Selected country does not exist", 400);
            }

            // Проверка клиента, если указан
            if (!empty($data['client_id'])) {
                $client = $db->fetchOne('SELECT id FROM clients WHERE id = ?', [$data['client_id']]);
                if (!$client) {
                    ErrorHandler::error("Selected client does not exist", 400);
                }
            }

            // Обновление
            $clientId = !empty($data['client_id']) ? $data['client_id'] : null;
            $db->execute(
                'UPDATE tours SET title = ?, country_id = ?, start_date = ?, price = ?, client_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
                [$data['title'], $data['country_id'], $data['start_date'], $data['price'], $clientId, $id]
            );

            $tour = $db->fetchOne(
                'SELECT t.*, c.name as country_name, cl.full_name as client_name FROM tours t
                 JOIN countries c ON t.country_id = c.id
                 LEFT JOIN clients cl ON t.client_id = cl.id
                 WHERE t.id = ?',
                [$id]
            );

            ErrorHandler::success($tour, 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error updating tour: " . $e->getMessage());
            ErrorHandler::error("Failed to update tour", 500);
        }
    }

    /**
     * DELETE удаление тура
     */
    public static function delete($id) {
        try {
            if (empty($id)) {
                ErrorHandler::error("Tour ID is required", 400);
            }

            $db = self::getDb();

            // Проверка существования тура
            $existing = $db->fetchOne('SELECT * FROM tours WHERE id = ?', [$id]);
            if (!$existing) {
                ErrorHandler::error("Tour not found", 404);
            }

            // Удаление
            $db->execute('DELETE FROM tours WHERE id = ?', [$id]);

            ErrorHandler::success(['id' => $id, 'deleted' => true], 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error deleting tour: " . $e->getMessage());
            ErrorHandler::error("Failed to delete tour", 500);
        }
    }

    /**
     * Проверка данных тура
     */
    private static function validateTourData($data) {
        $errors = [];

        if (empty($data['title'])) {
            $errors['title'] = 'Tour title is required';
        } elseif (strlen($data['title']) > 200) {
            $errors['title'] = 'Tour title must not exceed 200 characters';
        }

        if (empty($data['country_id'])) {
            $errors['country_id'] = 'Country is required';
        }

        if (empty($data['start_date'])) {
            $errors['start_date'] = 'Start date is required';
        } elseif (!self::isValidDate($data['start_date'])) {
            $errors['start_date'] = 'Start date must be in YYYY-MM-DD format';
        }

        if (empty($data['price'])) {
            $errors['price'] = 'Price is required';
        } elseif (!is_numeric($data['price']) || $data['price'] <= 0) {
            $errors['price'] = 'Price must be a positive number';
        }

        return $errors;
    }

    /**
     * Проверка формата даты (ГГГГ-ММ-ДД)
     */
    private static function isValidDate($date) {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
?>
