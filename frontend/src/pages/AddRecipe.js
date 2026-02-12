import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
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
    ErrorMessage,
} from "../styles/StyledComponents.js";

const AddRecipe = () => {
    const navigate = useNavigate();
    const { createRecipe, categories } = useRecipes();

    const [formData, setFormData] = useState({
        title: "",
        ingredients: "",
        instructions: "",
        cookingTime: "",
        category: "",
    });

    const [error, setError] = useState("");
    const [isSubmitting, setIsSubmitting] = useState(false);

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
        const result = await createRecipe(recipeData);

        setIsSubmitting(false);

        if (result) {
            // Успешно создано - переходим к списку рецептов
            navigate("/");
        } else {
            setError("Не удалось создать рецепт. Попробуйте снова.");
        }
    };

    return (
        <Container>
            <Title>Добавить новый рецепт</Title>

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

                <Button type="submit" disabled={isSubmitting}>
                    {isSubmitting ? "Сохранение..." : "Добавить рецепт"}
                </Button>
            </Form>
        </Container>
    );
};

export default AddRecipe;
