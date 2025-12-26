<template>
    <div class="not-found-view">
        <div class="container content-wrapper">
            <div class="status-code">404</div>
            <div class="divider"></div>
            <div class="info-block">
                <h1>Страница не найдена</h1>
                <p>
                    Извините, запрашиваемый адрес недоступен или страница была
                    перемещена.
                </p>
                <div class="actions">
                    <router-link to="/" class="btn-primary-small">
                        На главную
                    </router-link>
                    <button @click="$router.back()" class="btn-link-small">
                        Вернуться назад
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.not-found-view {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #ffffff;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

.content-wrapper {
    display: flex;
    align-items: center;
    gap: 40px;
    max-width: 800px;
}

.status-code {
    font-size: 80px;
    font-weight: 800;
    color: #1a1a1a;
    line-height: 1;
    letter-spacing: -0.05em;
}

.divider {
    width: 1px;
    height: 100px;
    background-color: #e0e0e0;
}

.info-block h1 {
    font-size: 24px;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0 0 12px 0;
}

.info-block p {
    font-size: 15px;
    color: #666;
    margin: 0 0 24px 0;
    max-width: 320px;
    line-height: 1.5;
}

.actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.btn-primary-small {
    background: #333;
    color: white;
    padding: 10px 20px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: background 0.2s;
}

.btn-primary-small:hover {
    background: #000;
}

.btn-link-small {
    background: none;
    border: none;
    color: #888;
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    padding: 0;
    transition: color 0.2s;
}

.btn-link-small:hover {
    color: #333;
    text-decoration: underline;
}

@media (max-width: 640px) {
    .content-wrapper {
        flex-direction: column;
        text-align: center;
        gap: 24px;
    }

    .divider {
        width: 60px;
        height: 1px;
    }

    .status-code {
        font-size: 64px;
    }

    .actions {
        justify-content: center;
    }
}
</style>
