import styled from "styled-components";
import { Link } from "react-router-dom";

// Цветовая палитра
export const colors = {
    primary: "#FF6B6B",
    secondary: "#4ECDC4",
    accent: "#FFE66D",
    dark: "#2C3E50",
    light: "#ECF0F1",
    white: "#FFFFFF",
    gray: "#95A5A6",
    success: "#2ECC71",
    danger: "#E74C3C",
};

// Общие стили контейнера
export const Container = styled.div`
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
`;

// Заголовки
export const Title = styled.h1`
    color: ${colors.dark};
    font-size: 2.5rem;
    margin-bottom: 20px;
    text-align: center;

    @media (max-width: 768px) {
        font-size: 2rem;
    }
`;

export const Subtitle = styled.h2`
    color: ${colors.dark};
    font-size: 1.8rem;
    margin-bottom: 15px;
`;

// Навигация
export const Nav = styled.nav`
    background-color: ${colors.primary};
    padding: 15px 0;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
`;

export const NavContainer = styled.div`
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
`;

export const NavBrand = styled(Link)`
    color: ${colors.white};
    font-size: 1.5rem;
    font-weight: bold;
    text-decoration: none;
    transition: opacity 0.3s;

    &:hover {
        opacity: 0.8;
    }
`;

export const NavLinks = styled.div`
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
`;

export const NavLink = styled(Link)`
    color: ${colors.white};
    text-decoration: none;
    padding: 8px 15px;
    border-radius: 5px;
    transition: background-color 0.3s;

    &:hover {
        background-color: rgba(255, 255, 255, 0.2);
    }

    &.active {
        background-color: rgba(255, 255, 255, 0.3);
    }
`;

// Карточки рецептов
export const RecipeGrid = styled.div`
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-top: 30px;
`;

export const RecipeCard = styled.div`
    background: ${colors.white};
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;

    &:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
    }
`;

export const RecipeTitle = styled.h3`
    color: ${colors.dark};
    font-size: 1.4rem;
    margin-bottom: 10px;
`;

export const RecipeCategory = styled.span`
    display: inline-block;
    background-color: ${colors.secondary};
    color: ${colors.white};
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.85rem;
    margin-bottom: 10px;
`;

export const RecipeTime = styled.div`
    color: ${colors.gray};
    font-size: 0.9rem;
    margin-bottom: 10px;

    &::before {
        content: "⏱️ ";
    }
`;

export const RecipeIngredients = styled.p`
    color: ${colors.dark};
    font-size: 0.95rem;
    line-height: 1.5;
    margin-bottom: 10px;
`;

// Кнопки
export const Button = styled.button`
    background-color: ${(props) =>
        props.variant === "danger" ? colors.danger : colors.primary};
    color: ${colors.white};
    border: none;
    padding: 12px 24px;
    border-radius: 5px;
    font-size: 1rem;
    cursor: pointer;
    transition: opacity 0.3s, transform 0.1s;

    &:hover {
        opacity: 0.9;
    }

    &:active {
        transform: scale(0.98);
    }

    &:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
`;

export const ButtonGroup = styled.div`
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 15px;
`;

// Формы
export const Form = styled.form`
    background: ${colors.white};
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    max-width: 600px;
    margin: 20px auto;
`;

export const FormGroup = styled.div`
    margin-bottom: 20px;
`;

export const Label = styled.label`
    display: block;
    color: ${colors.dark};
    font-weight: 500;
    margin-bottom: 8px;
`;

export const Input = styled.input`
    width: 100%;
    padding: 12px;
    border: 2px solid ${colors.light};
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;

    &:focus {
        outline: none;
        border-color: ${colors.primary};
    }
`;

export const TextArea = styled.textarea`
    width: 100%;
    padding: 12px;
    border: 2px solid ${colors.light};
    border-radius: 5px;
    font-size: 1rem;
    min-height: 120px;
    resize: vertical;
    font-family: inherit;
    transition: border-color 0.3s;

    &:focus {
        outline: none;
        border-color: ${colors.primary};
    }
`;

export const Select = styled.select`
    width: 100%;
    padding: 12px;
    border: 2px solid ${colors.light};
    border-radius: 5px;
    font-size: 1rem;
    transition: border-color 0.3s;
    background-color: ${colors.white};
    cursor: pointer;

    &:focus {
        outline: none;
        border-color: ${colors.primary};
    }
`;

// Детальная страница рецепта
export const RecipeDetail = styled.div`
    background: ${colors.white};
    padding: 30px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    max-width: 800px;
    margin: 20px auto;
`;

export const RecipeDetailTitle = styled.h2`
    color: ${colors.dark};
    font-size: 2rem;
    margin-bottom: 15px;
`;

export const RecipeDetailSection = styled.div`
    margin-bottom: 25px;
`;

export const SectionTitle = styled.h3`
    color: ${colors.primary};
    font-size: 1.3rem;
    margin-bottom: 10px;
`;

export const RecipeText = styled.p`
    color: ${colors.dark};
    line-height: 1.8;
    font-size: 1.05rem;
`;

// Состояния загрузки и ошибок
export const LoadingSpinner = styled.div`
    text-align: center;
    padding: 50px;
    font-size: 1.2rem;
    color: ${colors.gray};
`;

export const ErrorMessage = styled.div`
    background-color: ${colors.danger};
    color: ${colors.white};
    padding: 15px;
    border-radius: 5px;
    margin: 20px 0;
    text-align: center;
`;

export const EmptyState = styled.div`
    text-align: center;
    padding: 50px;
    color: ${colors.gray};
    font-size: 1.1rem;
`;

// Фильтры
export const FilterBar = styled.div`
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
`;

export const FilterLabel = styled.span`
    color: ${colors.dark};
    font-weight: 500;
`;
