import React from "react";
import { useRecipes } from "../context/RecipeContext.js";
import { FilterBar, FilterLabel, Select } from "../styles/StyledComponents.js";

const CategoryFilter = () => {
    const { categories, selectedCategory, setSelectedCategory } = useRecipes();

    const handleCategoryChange = (e) => {
        setSelectedCategory(e.target.value);
    };

    return (
        <FilterBar>
            <FilterLabel>Фильтр по категории:</FilterLabel>
            <Select value={selectedCategory} onChange={handleCategoryChange}>
                <option value="all">Все категории</option>
                {categories.map((category) => (
                    <option key={category} value={category}>
                        {category}
                    </option>
                ))}
            </Select>
        </FilterBar>
    );
};

export default CategoryFilter;
