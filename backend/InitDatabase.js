import sqlite3 from "sqlite3";
import { fileURLToPath } from "url";
import { dirname, join } from "path";

const __filename = fileURLToPath(import.meta.url);
const __dirname = dirname(__filename);

const DB_PATH = join(__dirname, "recipes.db");

/**
 * Скрипт создания таблиц в базе данных SQLite
 */
function initializeDatabase() {
    return new Promise((resolve, reject) => {
        const db = new sqlite3.Database(DB_PATH, (err) => {
            if (err) {
                console.error(
                    "❌ Ошибка подключения к базе данных:",
                    err.message
                );
                reject(err);
                return;
            }
            console.log("✅ Подключение к базе данных установлено");
        });

        // Создание таблицы recipes
        const createTableSQL = `
      CREATE TABLE IF NOT EXISTS recipes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        ingredients TEXT NOT NULL,
        instructions TEXT NOT NULL,
        cookingTime INTEGER,
        category TEXT,
        createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
        updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
      )
    `;

        db.run(createTableSQL, (err) => {
            if (err) {
                console.error("❌ Ошибка создания таблицы:", err.message);
                reject(err);
                return;
            }
            console.log("✅ Таблица recipes создана успешно");

            // Добавление тестовых данных
            const insertSQL = `
        INSERT INTO recipes (title, ingredients, instructions, cookingTime, category)
        VALUES (?, ?, ?, ?, ?)
      `;

            const sampleRecipes = [
                [
                    "Борщ",
                    "Свекла, капуста, картофель, морковь, лук, мясо",
                    "Сварить бульон, добавить овощи, варить до готовности",
                    90,
                    "Супы",
                ],
                [
                    "Паста Карбонара",
                    "Спагетти, бекон, яйца, пармезан, черный перец",
                    "Отварить пасту, обжарить бекон, смешать с яйцами и сыром",
                    20,
                    "Основные блюда",
                ],
                [
                    "Цезарь салат",
                    "Салат ромэн, курица, пармезан, сухарики, соус Цезарь",
                    "Нарезать ингредиенты, смешать с соусом",
                    15,
                    "Салаты",
                ],
            ];

            let completed = 0;
            sampleRecipes.forEach((recipe) => {
                db.run(insertSQL, recipe, (err) => {
                    if (err) {
                        console.error(
                            "⚠️ Ошибка добавления тестовых данных (возможно, они уже существуют)"
                        );
                    }
                    completed++;
                    if (completed === sampleRecipes.length) {
                        console.log("✅ Тестовые данные добавлены");
                        db.close((err) => {
                            if (err) {
                                console.error(
                                    "❌ Ошибка закрытия базы данных:",
                                    err.message
                                );
                                reject(err);
                            } else {
                                console.log(
                                    "✅ База данных инициализирована и закрыта"
                                );
                                resolve();
                            }
                        });
                    }
                });
            });
        });
    });
}

// Запуск инициализации
initializeDatabase()
    .then(() => {
        console.log("\n🎉 Инициализация базы данных завершена успешно!");
        process.exit(0);
    })
    .catch((err) => {
        console.error("\n💥 Ошибка инициализации:", err);
        process.exit(1);
    });
