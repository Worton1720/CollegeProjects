import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useRecipes } from "../context/RecipeContext.js";
import {
    Container,
    Title,
    Form,
    FormGroup,
    Label,
    Input,
    TextArea,
    Select,
    Button,
    LoadingSpinner,
    ErrorMessage,
} from "../styles/StyledComponents.js";

const EditRecipe = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { fetchRecipe, updateRecipe, categories } = useRecipes();

    const [formData, setFormData] = useState({
        title: "",
        ingredients: "",
        instructions: "",
        cookingTime: "",
        category: "",
    });

    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

    useEffect(() => {
        loadRecipe();
    }, [id]);

    const loadRecipe = async () => {
        setLoading(true);
        const recipe = await fetchRecipe(id);

        if (recipe) {
            setFormData({
                title: recipe.title,
                ingredients: recipe.ingredients,
                instructions: recipe.instructions,
                cookingTime: recipe.cookingTime || "",
                category: recipe.category || "",
            });
        } else {
            setError("Рецепт не найден");
        }

        setLoading(false);
    };

    const handleChange = (e) => {
        const { name, value } = e.target;
        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));
    };

    const handleSubmit = async (e) => {
        e.preventDefault();
        setError("");

        // Валидация
        if (!formData.title.trim()) {
            setError("Название рецепта обязательно");
            return;
        }
        if (!formData.ingredients.trim()) {
            setError("Ингредиенты обязательны");
            return;
        }
        if (!formData.instructions.trim()) {
            setError("Инструкции по приготовлению обязательны");
            return;
        }

        setIsSubmitting(true);

        // Подготовка данных
        const recipeData = {
            title: formData.title.trim(),
            ingredients: formData.ingredients.trim(),
            instructions: formData.instructions.trim(),
            cookingTime: formData.cookingTime
                ? parseInt(formData.cookingTime)
                : null,
            category: formData.category.trim() || null,
        };

        // Отправка на сервер
        const result = await updateRecipe(id, recipeData);

        setIsSubmitting(false);

        if (result) {
            // Успешно обновлено - переходим к просмотру рецепта
            navigate(`/recipe/${id}`);
        } else {
            setError("Не удалось обновить рецепт. Попробуйте снова.");
        }
    };

    if (loading) {
        return (
            <Container>
                <LoadingSpinner>Загрузка рецепта...</LoadingSpinner>
            </Container>
        );
    }

    return (
        <Container>
            <Title>Редактировать рецепт</Title>

            <Form onSubmit={handleSubmit}>
                {error && <ErrorMessage>{error}</ErrorMessage>}

                <FormGroup>
                    <Label htmlFor="title">Название рецепта *</Label>
                    <Input
                        type="text"
                        id="title"
                        name="title"
                        value={formData.title}
                        onChange={handleChange}
                        placeholder="Например: Борщ"
                        required
                    />
                </FormGroup>

                <FormGroup>
                    <Label htmlFor="category">Категория</Label>
                    <Select
                        id="category"
                        name="category"
                        value={formData.category}
                        onChange={handleChange}
                    >
                        <option value="">Выберите категорию</option>
                        <option value="Супы">Супы</option>
                        <option value="Основные блюда">Основные блюда</option>
                        <option value="Салаты">Салаты</option>
                        <option value="Десерты">Десерты</option>
                        <option value="Закуски">Закуски</option>
                        <option value="Выпечка">Выпечка</option>
                        {categories.map((cat) => (
                            <option key={cat} value={cat}>
                                {cat}
                            </option>
                        ))}
                    </Select>
                </FormGroup>

                <FormGroup>
                    <Label htmlFor="cookingTime">
                        Время приготовления (минуты)
                    </Label>
                    <Input
                        type="number"
                        id="cookingTime"
                        name="cookingTime"
                        value={formData.cookingTime}
                        onChange={handleChange}
                        placeholder="30"
                        min="1"
                    />
                </FormGroup>

                <FormGroup>
                    <Label htmlFor="ingredients">Ингредиенты *</Label>
                    <TextArea
                        id="ingredients"
                        name="ingredients"
                        value={formData.ingredients}
                        onChange={handleChange}
                        placeholder="Список ингредиентов (можно через запятую или каждый с новой строки)"
                        required
                    />
                </FormGroup>

                <FormGroup>
                    <Label htmlFor="instructions">
                        Инструкции по приготовлению *
                    </Label>
                    <TextArea
                        id="instructions"
                        name="instructions"
                        value={formData.instructions}
                        onChange={handleChange}
                        placeholder="Опишите пошаговый процесс приготовления"
                        required
                    />
                </FormGroup>

                <div style={{ display: "flex", gap: "10px" }}>
                    <Button type="submit" disabled={isSubmitting}>
                        {isSubmitting ? "Сохранение..." : "Сохранить изменения"}
                    </Button>
                    <Button
                        type="button"
                        onClick={() => navigate(`/recipe/${id}`)}
                    >
                        Отмена
                    </Button>
                </div>
            </Form>
        </Container>
    );
};

export default EditRecipe;
