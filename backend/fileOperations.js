import fs from "fs/promises";
import path from "path";
import { fileURLToPath } from "url";

const __filename = fileURLToPath(import.meta.url);
const __dirname = path.dirname(__filename);

const DATA_FILE = path.join(__dirname, "recipes_data.json");

/**
 * Записывает данные рецептов в файл
 */
export async function writeRecipesToFile(recipes) {
    try {
        const data = JSON.stringify(recipes, null, 2);
        await fs.writeFile(DATA_FILE, data, "utf-8");
        console.log(`✅ Данные успешно записаны в файл: ${DATA_FILE}`);
        return true;
    } catch (error) {
        console.error("❌ Ошибка записи в файл:", error.message);
        return false;
    }
}

/**
 * Читает данные рецептов из файла
 */
export async function readRecipesFromFile() {
    try {
        const data = await fs.readFile(DATA_FILE, "utf-8");
        const recipes = JSON.parse(data);
        console.log("✅ Данные успешно прочитаны из файла:");
        console.log(data); 
        return recipes;
    } catch (error) {
        if (error.code === "ENOENT") {
            console.log("ℹ️ Файл еще не существует.");
            return [];
        }
        console.error("❌ Ошибка чтения файла:", error.message);
        return [];
    }
}

export const sampleRecipes = [
    {
        id: 1,
        title: "Борщ",
        ingredients: "Свекла, капуста, мясо",
        instructions: "Варить до готовности",
        cookingTime: 90,
        category: "Супы",
    },
    {
        id: 2,
        title: "Паста Карбонара",
        ingredients: "Спагетти, бекон, яйца",
        instructions: "Смешать и подавать",
        cookingTime: 20,
        category: "Основные блюда",
    }
];

const start = async () => {
    const isMain = process.argv[1] && fileURLToPath(import.meta.url) === path.resolve(process.argv[1]);
    
    if (isMain) {
        console.log("🚀 Запуск тестирования модуля работы с файлами...\n");

        console.log("📝 Запись тестовых данных...");
        await writeRecipesToFile(sampleRecipes);

        console.log("\n📖 Чтение данных для проверки...");
        await readRecipesFromFile();

        console.log("\n✅ Тестирование завершено!");
    }
};

start().catch(err => console.error("💥 Критическая ошибка:", err));