import express from "express";
import cors from "cors";
import Database from "./database.js";

const app = express();
const PORT = 3001;

// Middleware
app.use(cors());
app.use(express.json());

// Инициализация базы данных
const db = new Database();
await db.connect();

console.log("✅ Сервер подключен к базе данных");

/**
 * ЗАДАНИЕ 2: Обработчики базовых операций CRUD
 */

// а) Создание новой записи
app.post("/api/recipes", async (req, res) => {
    try {
        const { title, ingredients, instructions, cookingTime, category } =
            req.body;

        // Валидация
        if (!title || !ingredients || !instructions) {
            return res.status(400).json({
                error: "Обязательные поля: title, ingredients, instructions",
            });
        }

        const sql = `
      INSERT INTO recipes (title, ingredients, instructions, cookingTime, category)
      VALUES (?, ?, ?, ?, ?)
    `;

        const result = await db.run(sql, [
            title,
            ingredients,
            instructions,
            cookingTime || null,
            category || null,
        ]);

        // Получаем созданный рецепт
        const newRecipe = await db.get("SELECT * FROM recipes WHERE id = ?", [
            result.id,
        ]);

        res.status(201).json({
            message: "Рецепт успешно создан",
            recipe: newRecipe,
        });
    } catch (error) {
        console.error("Ошибка создания рецепта:", error);
        res.status(500).json({ error: "Ошибка сервера при создании рецепта" });
    }
});

// б) Получение списка всех записей
app.get("/api/recipes", async (req, res) => {
    try {
        const { category } = req.query;

        let sql = "SELECT * FROM recipes ORDER BY createdAt DESC";
        let params = [];

        // Фильтрация по категории (опционально)
        if (category) {
            sql =
                "SELECT * FROM recipes WHERE category = ? ORDER BY createdAt DESC";
            params = [category];
        }

        const recipes = await db.all(sql, params);

        res.json({
            count: recipes.length,
            recipes: recipes,
        });
    } catch (error) {
        console.error("Ошибка получения списка рецептов:", error);
        res.status(500).json({
            error: "Ошибка сервера при получении списка рецептов",
        });
    }
});

// в) Получение одной записи по id
app.get("/api/recipes/:id", async (req, res) => {
    try {
        const { id } = req.params;

        const recipe = await db.get("SELECT * FROM recipes WHERE id = ?", [id]);

        if (!recipe) {
            return res.status(404).json({ error: "Рецепт не найден" });
        }

        res.json({ recipe });
    } catch (error) {
        console.error("Ошибка получения рецепта:", error);
        res.status(500).json({ error: "Ошибка сервера при получении рецепта" });
    }
});

/**
 * ЗАДАНИЕ 3: Обработчики удаления и обновления
 */

// Удаление записи по id
app.delete("/api/recipes/:id", async (req, res) => {
    try {
        const { id } = req.params;

        // Проверяем существование рецепта
        const recipe = await db.get("SELECT * FROM recipes WHERE id = ?", [id]);

        if (!recipe) {
            return res.status(404).json({ error: "Рецепт не найден" });
        }

        // Удаляем рецепт
        await db.run("DELETE FROM recipes WHERE id = ?", [id]);

        res.json({
            message: "Рецепт успешно удален",
            deletedRecipe: recipe,
        });
    } catch (error) {
        console.error("Ошибка удаления рецепта:", error);
        res.status(500).json({ error: "Ошибка сервера при удалении рецепта" });
    }
});

// Обновление записи по id (метод PATCH)
app.patch("/api/recipes/:id", async (req, res) => {
    try {
        const { id } = req.params;
        const { title, ingredients, instructions, cookingTime, category } =
            req.body;

        // Проверяем существование рецепта
        const recipe = await db.get("SELECT * FROM recipes WHERE id = ?", [id]);

        if (!recipe) {
            return res.status(404).json({ error: "Рецепт не найден" });
        }

        // Формируем SQL запрос для обновления только переданных полей
        const updates = [];
        const values = [];

        if (title !== undefined) {
            updates.push("title = ?");
            values.push(title);
        }
        if (ingredients !== undefined) {
            updates.push("ingredients = ?");
            values.push(ingredients);
        }
        if (instructions !== undefined) {
            updates.push("instructions = ?");
            values.push(instructions);
        }
        if (cookingTime !== undefined) {
            updates.push("cookingTime = ?");
            values.push(cookingTime);
        }
        if (category !== undefined) {
            updates.push("category = ?");
            values.push(category);
        }

        // Добавляем обновление времени
        updates.push("updatedAt = CURRENT_TIMESTAMP");

        if (updates.length === 1) {
            // только updatedAt
            return res
                .status(400)
                .json({ error: "Не указаны поля для обновления" });
        }

        values.push(id); // Добавляем id для WHERE

        const sql = `UPDATE recipes SET ${updates.join(", ")} WHERE id = ?`;
        await db.run(sql, values);

        // Получаем обновленный рецепт
        const updatedRecipe = await db.get(
            "SELECT * FROM recipes WHERE id = ?",
            [id]
        );

        res.json({
            message: "Рецепт успешно обновлен",
            recipe: updatedRecipe,
        });
    } catch (error) {
        console.error("Ошибка обновления рецепта:", error);
        res.status(500).json({
            error: "Ошибка сервера при обновлении рецепта",
        });
    }
});

// Получение списка категорий
app.get("/api/categories", async (req, res) => {
    try {
        const categories = await db.all(
            "SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL ORDER BY category"
        );

        res.json({
            categories: categories.map((c) => c.category),
        });
    } catch (error) {
        console.error("Ошибка получения категорий:", error);
        res.status(500).json({
            error: "Ошибка сервера при получении категорий",
        });
    }
});

// Корневой маршрут
app.get("/", (req, res) => {
    res.json({
        message: "Recipe Manager API",
        version: "1.0.0",
        endpoints: {
            "GET /api/recipes": "Получить все рецепты",
            "GET /api/recipes/:id": "Получить рецепт по ID",
            "POST /api/recipes": "Создать новый рецепт",
            "PATCH /api/recipes/:id": "Обновить рецепт",
            "DELETE /api/recipes/:id": "Удалить рецепт",
            "GET /api/categories": "Получить список категорий",
        },
    });
});

// Обработка несуществующих маршрутов
app.use((req, res) => {
    res.status(404).json({ error: "Маршрут не найден" });
});

// Запуск сервера
app.listen(PORT, () => {
    console.log(`🚀 Сервер запущен на порту ${PORT}`);
    console.log(`📍 API доступен по адресу: http://localhost:${PORT}`);
    console.log(`📚 Документация: http://localhost:${PORT}/`);
});

// Graceful shutdown
process.on("SIGINT", async () => {
    console.log("\n⏹️ Остановка сервера...");
    await db.close();
    console.log("✅ База данных закрыта");
    process.exit(0);
});
