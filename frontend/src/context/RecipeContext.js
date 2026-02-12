import React, { createContext, useState, useContext, useEffect } from "react";

const API_URL = "http://localhost:3001/api";

// Создание контекста
const RecipeContext = createContext();

// Хук для использования контекста
export const useRecipes = () => {
    const context = useContext(RecipeContext);
    if (!context) {
        throw new Error(
            "useRecipes должен использоваться внутри RecipeProvider"
        );
    }
    return context;
};

// Провайдер контекста
export const RecipeProvider = ({ children }) => {
    const [recipes, setRecipes] = useState([]);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [selectedCategory, setSelectedCategory] = useState("all");

    // Загрузка всех рецептов
    const fetchRecipes = async (category = null) => {
        setLoading(true);
        setError(null);
        try {
            const url =
                category && category !== "all"
                    ? `${API_URL}/recipes?category=${encodeURIComponent(
                          category
                      )}`
                    : `${API_URL}/recipes`;

            const response = await fetch(url);
            if (!response.ok) throw new Error("Ошибка загрузки рецептов");

            const data = await response.json();
            setRecipes(data.recipes);
        } catch (err) {
            setError(err.message);
            console.error("Ошибка загрузки рецептов:", err);
        } finally {
            setLoading(false);
        }
    };

    // Загрузка категорий
    const fetchCategories = async () => {
        try {
            const response = await fetch(`${API_URL}/categories`);
            if (!response.ok) throw new Error("Ошибка загрузки категорий");

            const data = await response.json();
            setCategories(data.categories);
        } catch (err) {
            console.error("Ошибка загрузки категорий:", err);
        }
    };

    // Получение одного рецепта
    const fetchRecipe = async (id) => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(`${API_URL}/recipes/${id}`);
            if (!response.ok) throw new Error("Рецепт не найден");

            const data = await response.json();
            return data.recipe;
        } catch (err) {
            setError(err.message);
            console.error("Ошибка загрузки рецепта:", err);
            return null;
        } finally {
            setLoading(false);
        }
    };

    // Создание нового рецепта
    const createRecipe = async (recipeData) => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(`${API_URL}/recipes`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(recipeData),
            });

            if (!response.ok) throw new Error("Ошибка создания рецепта");

            const data = await response.json();
            setRecipes([data.recipe, ...recipes]);
            await fetchCategories(); // Обновляем список категорий
            return data.recipe;
        } catch (err) {
            setError(err.message);
            console.error("Ошибка создания рецепта:", err);
            return null;
        } finally {
            setLoading(false);
        }
    };

    // Обновление рецепта
    const updateRecipe = async (id, recipeData) => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(`${API_URL}/recipes/${id}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify(recipeData),
            });

            if (!response.ok) throw new Error("Ошибка обновления рецепта");

            const data = await response.json();
            setRecipes(recipes.map((r) => (r.id === id ? data.recipe : r)));
            await fetchCategories(); // Обновляем список категорий
            return data.recipe;
        } catch (err) {
            setError(err.message);
            console.error("Ошибка обновления рецепта:", err);
            return null;
        } finally {
            setLoading(false);
        }
    };

    // Удаление рецепта
    const deleteRecipe = async (id) => {
        setLoading(true);
        setError(null);
        try {
            const response = await fetch(`${API_URL}/recipes/${id}`, {
                method: "DELETE",
            });

            if (!response.ok) throw new Error("Ошибка удаления рецепта");

            setRecipes(recipes.filter((r) => r.id !== id));
            await fetchCategories(); // Обновляем список категорий
            return true;
        } catch (err) {
            setError(err.message);
            console.error("Ошибка удаления рецепта:", err);
            return false;
        } finally {
            setLoading(false);
        }
    };

    // Загрузка данных при монтировании
    useEffect(() => {
        fetchRecipes();
        fetchCategories();
    }, []);

    // Обновление рецептов при изменении категории
    useEffect(() => {
        fetchRecipes(selectedCategory);
    }, [selectedCategory]);

    const value = {
        recipes,
        categories,
        loading,
        error,
        selectedCategory,
        setSelectedCategory,
        fetchRecipes,
        fetchRecipe,
        createRecipe,
        updateRecipe,
        deleteRecipe,
    };

    return (
        <RecipeContext.Provider value={value}>
            {children}
        </RecipeContext.Provider>
    );
};
