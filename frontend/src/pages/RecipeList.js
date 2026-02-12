import React, { useEffect } from "react";
import { useRecipes } from "../context/RecipeContext.js";
import RecipeCard from "../components/RecipeCard.js";
import CategoryFilter from "../components/CategoryFilter.js";
import {
    Container,
    Title,
    RecipeGrid,
    LoadingSpinner,
    ErrorMessage,
    EmptyState,
} from "../styles/StyledComponents.js";

const RecipeList = () => {
    const { recipes, loading, error, fetchRecipes } = useRecipes();

    useEffect(() => {
        fetchRecipes();
    }, []);

    if (loading) {
        return (
            <Container>
                <LoadingSpinner>Загрузка рецептов...</LoadingSpinner>
            </Container>
        );
    }

    if (error) {
        return (
            <Container>
                <ErrorMessage>Ошибка: {error}</ErrorMessage>
            </Container>
        );
    }

    return (
        <Container>
            <Title>Книга рецептов</Title>
            <button onClick={() => fetchRecipes()}>Обновить вручную</button>
            <CategoryFilter />

            {recipes.length === 0 ? (
                <EmptyState>
                    📖 Рецепты не найдены. Добавьте свой первый рецепт!
                </EmptyState>
            ) : (
                <RecipeGrid>
                    {recipes.map((recipe) => (
                        <RecipeCard key={recipe.id} recipe={recipe} />
                    ))}
                </RecipeGrid>
            )}
        </Container>
    );
};

export default RecipeList;
