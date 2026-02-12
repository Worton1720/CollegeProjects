import React from "react";
import { useNavigate } from "react-router-dom";
import {
    RecipeCard as Card,
    RecipeTitle,
    RecipeCategory,
    RecipeTime,
    RecipeIngredients,
} from "../styles/StyledComponents.js";

const RecipeCard = ({ recipe }) => {
    const navigate = useNavigate();

    const handleClick = () => {
        navigate(`/recipe/${recipe.id}`);
    };

    // Сокращаем текст ингредиентов для превью
    const shortIngredients =
        recipe.ingredients.length > 100
            ? recipe.ingredients.substring(0, 100) + "..."
            : recipe.ingredients;

    return (
        <Card onClick={handleClick}>
            <RecipeTitle>{recipe.title}</RecipeTitle>
            {recipe.category && (
                <RecipeCategory>{recipe.category}</RecipeCategory>
            )}
            {recipe.cookingTime && (
                <RecipeTime>{recipe.cookingTime} минут</RecipeTime>
            )}
            <RecipeIngredients>
                <strong>Ингредиенты:</strong> {shortIngredients}
            </RecipeIngredients>
        </Card>
    );
};

export default RecipeCard;
