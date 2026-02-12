import React from "react";
import { BrowserRouter as Router, Routes, Route } from "react-router-dom";
import { RecipeProvider } from "./context/RecipeContext.js";
import Header from "./components/Header.js";
import RecipeList from "./pages/RecipeList.js";
import AddRecipe from "./pages/AddRecipe.js";
import RecipeDetailPage from "./pages/RecipeDetailPage.js";
import EditRecipe from "./pages/EditRecipe.js";
import { createGlobalStyle } from "styled-components";

// Глобальные стили
const GlobalStyle = createGlobalStyle`
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
  }

  body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen',
      'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue',
      sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    background-color: #f5f5f5;
  }

  code {
    font-family: source-code-pro, Menlo, Monaco, Consolas, 'Courier New',
      monospace;
  }
`;

function App() {
    return (
        <Router>
            <RecipeProvider>
                <GlobalStyle />
                <div className="App">
                    <Header />
                    <Routes>
                        <Route path="/" element={<RecipeList />} />
                        <Route path="/add" element={<AddRecipe />} />
                        <Route
                            path="/recipe/:id"
                            element={<RecipeDetailPage />}
                        />
                        <Route path="/edit/:id" element={<EditRecipe />} />
                    </Routes>
                </div>
            </RecipeProvider>
        </Router>
    );
}

export default App;
