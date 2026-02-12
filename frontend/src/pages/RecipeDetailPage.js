import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { useRecipes } from "../context/RecipeContext.js";
import {
    Container,
    RecipeDetail,
    RecipeDetailTitle,
    RecipeDetailSection,
    SectionTitle,
    RecipeText,
    RecipeCategory,
    RecipeTime,
    ButtonGroup,
    Button,
    LoadingSpinner,
    ErrorMessage,
} from "../styles/StyledComponents.js";

const RecipeDetailPage = () => {
    const { id } = useParams();
    const navigate = useNavigate();
    const { fetchRecipe, deleteRecipe } = useRecipes();

    const [recipe, setRecipe] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");

    useEffect(() => {
        loadRecipe();
    }, [id]);

    const loadRecipe = async () => {
        setLoading(true);
        setError("");

        const data = await fetchRecipe(id);

        if (data) {
            setRecipe(data);
        } else {
            setError("Рецепт не найден");
        }

        setLoading(false);
    };

    const handleDelete = async () => {
        if (window.confirm("Вы уверены, что хотите удалить этот рецепт?")) {
            const success = await deleteRecipe(id);
            if (success) {
                navigate("/");
            } else {
                setError("Не удалось удалить рецепт");
            }
        }
    };

    const handleEdit = () => {
        navigate(`/edit/${id}`);
    };

    if (loading) {
        return (
            <Container>
                <LoadingSpinner>Загрузка рецепта...</LoadingSpinner>
            </Container>
        );
    }

    if (error || !recipe) {
        return (
            <Container>
                <ErrorMessage>{error || "Рецепт не найден"}</ErrorMessage>
                <Button onClick={() => navigate("/")}>
                    Вернуться к списку
                </Button>
            </Container>
        );
    }

    return (
        <Container>
            <RecipeDetail>
                <RecipeDetailTitle>{recipe.title}</RecipeDetailTitle>

                <div style={{ marginBottom: "20px" }}>
                    {recipe.category && (
                        <RecipeCategory>{recipe.category}</RecipeCategory>
                    )}
                    {recipe.cookingTime && (
                        <RecipeTime>{recipe.cookingTime} минут</RecipeTime>
                    )}
                </div>

                <RecipeDetailSection>
                    <SectionTitle>Ингредиенты</SectionTitle>
                    <RecipeText>{recipe.ingredients}</RecipeText>
                </RecipeDetailSection>

                <RecipeDetailSection>
                    <SectionTitle>Инструкции по приготовлению</SectionTitle>
                    <RecipeText>{recipe.instructions}</RecipeText>
                </RecipeDetailSection>

                <ButtonGroup>
                    <Button onClick={handleEdit}>Редактировать</Button>
                    <Button variant="danger" onClick={handleDelete}>
                        Удалить
                    </Button>
                    <Button onClick={() => navigate("/")}>
                        Назад к списку
                    </Button>
                </ButtonGroup>
            </RecipeDetail>
        </Container>
    );
};

export default RecipeDetailPage;
