import React from "react";
import { useLocation } from "react-router-dom";
import {
    Nav,
    NavContainer,
    NavBrand,
    NavLinks,
    NavLink,
} from "../styles/StyledComponents.js";

const Header = () => {
    const location = useLocation();

    return (
        <Nav>
            <NavContainer>
                <NavBrand to="/">🍳 Менеджер рецептов</NavBrand>
                <NavLinks>
                    <NavLink
                        to="/"
                        className={location.pathname === "/" ? "active" : ""}
                    >
                        Все рецепты
                    </NavLink>
                    <NavLink
                        to="/add"
                        className={location.pathname === "/add" ? "active" : ""}
                    >
                        Добавить рецепт
                    </NavLink>
                </NavLinks>
            </NavContainer>
        </Nav>
    );
};

export default Header;
