<?php
/**
 * Обработчик клиентов
 * Управляет CRUD операциями для клиентов
 */

class ClientsHandler {
    private static $db = null;

    private static function getDb() {
        if (self::$db === null) {
            self::$db = Database::getInstance();
        }
        return self::$db;
    }

    /**
     * GET все клиенты
     */
    public static function getAll() {
        try {
            $db = self::getDb();
            $sql = "
                SELECT c.*, t.title as assigned_tour
                FROM clients c
                LEFT JOIN tours t ON c.id = t.client_id
                ORDER BY c.full_name ASC
            ";
            $clients = $db->fetchAll($sql);
            ErrorHandler::success($clients, 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error fetching clients: " . $e->getMessage());
            ErrorHandler::error("Failed to fetch clients", 500);
        }
    }

    /**
     * POST создание нового клиента
     */
    public static function create() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = self::validateClientData($data);
            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            $db = self::getDb();

            // Вставка
            $db->execute(
                'INSERT INTO clients (full_name, phone, passport_data) VALUES (?, ?, ?)',
                [$data['full_name'], $data['phone'], $data['passport_data']]
            );

            $id = $db->lastInsertId();
            $client = $db->fetchOne('SELECT * FROM clients WHERE id = ?', [$id]);

            ErrorHandler::success($client, 201);
        } catch (Exception $e) {
            ErrorHandler::log("Error creating client: " . $e->getMessage());
            ErrorHandler::error("Failed to create client", 500);
        }
    }

    /**
     * POST быстрое добавление клиента и назначение на тур
     */
    public static function quickAdd() {
        try {
            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = self::validateClientData($data);
            if (empty($data['tour_id'])) {
                $errors['tour_id'] = 'Tour ID is required';
            }

            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            $db = self::getDb();

            // Проверка существования тура
            $tour = $db->fetchOne('SELECT id, client_id FROM tours WHERE id = ?', [$data['tour_id']]);
            if (!$tour) {
                ErrorHandler::error("Tour not found", 404);
            }

            // Начало транзакции
            $db->beginTransaction();

            try {
                // Создание клиента
                $db->execute(
                    'INSERT INTO clients (full_name, phone, passport_data) VALUES (?, ?, ?)',
                    [$data['full_name'], $data['phone'], $data['passport_data']]
                );

                $clientId = $db->lastInsertId();

                // Назначение на тур
                $db->execute(
                    'UPDATE tours SET client_id = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
                    [$clientId, $data['tour_id']]
                );

                // Подтверждение транзакции
                $db->commit();

                $client = $db->fetchOne('SELECT * FROM clients WHERE id = ?', [$clientId]);
                $updatedTour = $db->fetchOne('SELECT * FROM tours WHERE id = ?', [$data['tour_id']]);

                ErrorHandler::success([
                    'client' => $client,
                    'tour' => $updatedTour
                ], 201);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            ErrorHandler::log("Error in quick-add: " . $e->getMessage());
            ErrorHandler::error("Failed to create and assign client", 500);
        }
    }

    /**
     * PUT обновление клиента
     */
    public static function update($id) {
        try {
            if (empty($id)) {
                ErrorHandler::error("Client ID is required", 400);
            }

            $db = self::getDb();

            // Проверка существования клиента
            $existing = $db->fetchOne('SELECT * FROM clients WHERE id = ?', [$id]);
            if (!$existing) {
                ErrorHandler::error("Client not found", 404);
            }

            $data = json_decode(file_get_contents('php://input'), true);

            // Валидация
            $errors = self::validateClientData($data);
            if (!empty($errors)) {
                ErrorHandler::validationError($errors);
            }

            // Обновление
            $db->execute(
                'UPDATE clients SET full_name = ?, phone = ?, passport_data = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?',
                [$data['full_name'], $data['phone'], $data['passport_data'], $id]
            );

            $client = $db->fetchOne('SELECT * FROM clients WHERE id = ?', [$id]);
            ErrorHandler::success($client, 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error updating client: " . $e->getMessage());
            ErrorHandler::error("Failed to update client", 500);
        }
    }

    /**
     * DELETE удаление клиента
     */
    public static function delete($id) {
        try {
            if (empty($id)) {
                ErrorHandler::error("Client ID is required", 400);
            }

            $db = self::getDb();

            // Проверка существования клиента
            $existing = $db->fetchOne('SELECT * FROM clients WHERE id = ?', [$id]);
            if (!$existing) {
                ErrorHandler::error("Client not found", 404);
            }

            // Снятие с назначений на туры
            $db->execute('UPDATE tours SET client_id = NULL WHERE client_id = ?', [$id]);

            // Удаление
            $db->execute('DELETE FROM clients WHERE id = ?', [$id]);

            ErrorHandler::success(['id' => $id, 'deleted' => true], 200);
        } catch (Exception $e) {
            ErrorHandler::log("Error deleting client: " . $e->getMessage());
            ErrorHandler::error("Failed to delete client", 500);
        }
    }

    /**
     * Проверка данных клиента
     */
    private static function validateClientData($data) {
        $errors = [];

        if (empty($data['full_name'])) {
            $errors['full_name'] = 'Full name is required';
        } elseif (strlen($data['full_name']) > 200) {
            $errors['full_name'] = 'Full name must not exceed 200 characters';
        }

        if (empty($data['phone'])) {
            $errors['phone'] = 'Phone is required';
        } elseif (strlen($data['phone']) > 20) {
            $errors['phone'] = 'Phone must not exceed 20 characters';
        }

        if (empty($data['passport_data'])) {
            $errors['passport_data'] = 'Passport data is required';
        } elseif (strlen($data['passport_data']) > 100) {
            $errors['passport_data'] = 'Passport data must not exceed 100 characters';
        }

        return $errors;
    }
}
?>
