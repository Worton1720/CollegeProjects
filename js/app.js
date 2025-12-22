/**
 * Main Application Logic
 * Initializes app and handles page navigation
 */

document.addEventListener('DOMContentLoaded', async () => {
    console.log('Application initialized');

    // Check if user is authenticated
    try {
        const response = await fetch('/api/index.php?entity=auth&action=check');
        const data = await response.json();

        if (!data.data.authenticated) {
            console.log('Not authenticated - redirecting to login');
            window.location.href = '/login.html';
            return;
        }

        console.log('Authenticated as:', data.data.username);
    } catch (error) {
        console.error('Auth check failed:', error);
        window.location.href = '/login.html';
        return;
    }

    // Initialize app - load tours page by default
    loadPage('tours');
});

/**
 * Load a page (tours, clients, countries)
 */
async function loadPage(page) {
    try {
        console.group(`Loading page: ${page}`);
        const contentDiv = document.getElementById('content');
        contentDiv.innerHTML = '<p>Загрузка...</p>';

        switch (page) {
            case 'tours':
                console.log('Rendering tours table...');
                UI.renderToursTable();
                break;
            case 'clients':
                console.log('Rendering clients table...');
                UI.renderClientsTable();
                break;
            case 'countries':
                console.log('Rendering countries table...');
                UI.renderCountriesTable();
                break;
            default:
                console.warn(`Unknown page: ${page}`);
                contentDiv.innerHTML = '<p>Страница не найдена</p>';
        }

        // Update active nav link
        document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
            link.classList.remove('active');
            const href = link.getAttribute('onclick');
            if (href && href.includes(`'${page}'`)) {
                link.classList.add('active');
            }
        });

        console.log(`Page ${page} loaded successfully`);
        console.groupEnd();
    } catch (error) {
        console.error(`Error loading page ${page}:`, error);
        document.getElementById('content').innerHTML = '<p>Ошибка загрузки страницы: ' + error.message + '</p>';
        console.groupEnd();
    }
}

// Additional UI methods for clients page
UI.renderClientsTable = async function() {
    try {
        const contentDiv = document.getElementById('content');
        contentDiv.innerHTML = '<p>Загрузка клиентов...</p>';

        const clients = await ApiClient.Clients.getAll();

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Клиенты</h2>
                <button class="btn btn-primary" onclick="UI.showClientForm()">+ Добавить клиента</button>
            </div>

            <div id="client-form-container" style="display: none;"></div>
        `;

        if (clients.length === 0) {
            html += '<div class="empty-message"><h5>Клиентов пока нет</h5><p>Создайте первого клиента</p></div>';
        } else {
            html += `
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ФИО</th>
                            <th>Телефон</th>
                            <th>Паспортные данные</th>
                            <th>Назначенный тур</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            clients.forEach(client => {
                const assignedTour = client.assigned_tour || 'Нет';
                html += `
                    <tr>
                        <td>${client.full_name}</td>
                        <td>${client.phone}</td>
                        <td>${client.passport_data}</td>
                        <td>${assignedTour}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="UI.showClientForm(${client.id})">Редактировать</button>
                            <button class="btn btn-sm btn-danger" onclick="UI.deleteClient(${client.id})">Удалить</button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
        }

        contentDiv.innerHTML = html;
    } catch (error) {
        UI.showError('Не удалось загрузить клиентов: ' + error.message);
    }
};

UI.showClientForm = async function(clientId = null) {
    try {
        const formContainer = document.getElementById('client-form-container');

        const html = `
            <div class="card mb-4">
                <div class="card-header">${clientId ? 'Редактирование клиента' : 'Добавить нового клиента'}</div>
                <div class="card-body">
                    <form id="client-form">
                        <div class="form-group">
                            <label class="form-label" for="client-full-name">ФИО</label>
                            <input type="text" id="client-full-name" class="form-control" placeholder="ФИО">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="client-phone">Телефон</label>
                            <input type="text" id="client-phone" class="form-control" placeholder="Номер телефона">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="client-passport">Паспортные данные</label>
                            <input type="text" id="client-passport" class="form-control" placeholder="Паспортная информация">
                        </div>

                        <button type="button" class="btn btn-primary" onclick="UI.saveClient(${clientId || 'null'})">Сохранить</button>
                        <button type="button" class="btn btn-secondary" onclick="UI.cancelClientForm()">Отмена</button>
                    </form>
                </div>
            </div>
        `;

        formContainer.innerHTML = html;
        formContainer.style.display = 'block';

        Validation.attachBlurListeners('client-form', (fieldId, value) => {
            return Validation.validateClientField(fieldId.replace('client-', ''), value);
        });
    } catch (error) {
        UI.showError('Не удалось загрузить форму: ' + error.message);
    }
};

UI.saveClient = async function(clientId) {
    try {
        const isValid = Validation.validateForm('client-form', (fieldId, value) => {
            return Validation.validateClientField(fieldId.replace('client-', ''), value);
        });

        if (!isValid) return;

        const data = {
            full_name: document.getElementById('client-full-name').value,
            phone: document.getElementById('client-phone').value,
            passport_data: document.getElementById('client-passport').value
        };

        if (clientId) {
            await ApiClient.Clients.update(clientId, data);
            UI.showSuccess('Клиент обновлен');
        } else {
            await ApiClient.Clients.create(data);
            UI.showSuccess('Клиент создан');
        }

        UI.cancelClientForm();
        UI.renderClientsTable();
    } catch (error) {
        UI.showError('Не удалось сохранить клиента: ' + error.message);
    }
};

UI.cancelClientForm = function() {
    document.getElementById('client-form-container').innerHTML = '';
    document.getElementById('client-form-container').style.display = 'none';
};

UI.deleteClient = async function(clientId) {
    if (!confirm('Удалить этого клиента?')) return;
    try {
        await ApiClient.Clients.delete(clientId);
        UI.showSuccess('Клиент удален');
        UI.renderClientsTable();
    } catch (error) {
        UI.showError('Не удалось удалить: ' + error.message);
    }
};

// Countries page
UI.renderCountriesTable = async function() {
    try {
        const contentDiv = document.getElementById('content');
        contentDiv.innerHTML = '<p>Загрузка стран...</p>';

        const countries = await ApiClient.Countries.getAll();

        let html = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Страны</h2>
                <button class="btn btn-primary" onclick="UI.showCountryForm()">+ Добавить страну</button>
            </div>

            <div id="country-form-container" style="display: none;"></div>
        `;

        if (countries.length === 0) {
            html += '<div class="empty-message"><h5>Стран нет</h5><p>Создайте первую страну</p></div>';
        } else {
            html += `
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Страна</th>
                            <th>Требуется виза</th>
                            <th>Действия</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

            countries.forEach(country => {
                const visaStatus = country.visa_required ? 'Да' : 'Нет';
                html += `
                    <tr>
                        <td>${country.name}</td>
                        <td>${visaStatus}</td>
                        <td>
                            <button class="btn btn-sm btn-warning" onclick="UI.showCountryForm(${country.id})">Редактировать</button>
                            <button class="btn btn-sm btn-danger" onclick="UI.deleteCountry(${country.id})">Удалить</button>
                        </td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
        }

        contentDiv.innerHTML = html;
    } catch (error) {
        UI.showError('Не удалось загрузить страны: ' + error.message);
    }
};

UI.showCountryForm = async function(countryId = null) {
    try {
        const formContainer = document.getElementById('country-form-container');

        const html = `
            <div class="card mb-4">
                <div class="card-header">${countryId ? 'Редактирование страны' : 'Добавить новую страну'}</div>
                <div class="card-body">
                    <form id="country-form">
                        <div class="form-group">
                            <label class="form-label" for="country-name">Название страны</label>
                            <input type="text" id="country-name" class="form-control" placeholder="Название страны">
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" id="country-visa" class="form-check-input">
                                <label class="form-check-label" for="country-visa">Требуется виза</label>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary" onclick="UI.saveCountry(${countryId || 'null'})">Сохранить</button>
                        <button type="button" class="btn btn-secondary" onclick="UI.cancelCountryForm()">Отмена</button>
                    </form>
                </div>
            </div>
        `;

        formContainer.innerHTML = html;
        formContainer.style.display = 'block';

        Validation.attachBlurListeners('country-form', (fieldId, value) => {
            return Validation.validateCountryField(fieldId.replace('country-', ''), value);
        });
    } catch (error) {
        UI.showError('Не удалось загрузить форму: ' + error.message);
    }
};

UI.saveCountry = async function(countryId) {
    try {
        const isValid = Validation.validateForm('country-form', (fieldId, value) => {
            return Validation.validateCountryField(fieldId.replace('country-', ''), value);
        });

        if (!isValid) return;

        const data = {
            name: document.getElementById('country-name').value,
            visa_required: document.getElementById('country-visa').checked ? 1 : 0
        };

        if (countryId) {
            await ApiClient.Countries.update(countryId, data);
            UI.showSuccess('Страна обновлена');
        } else {
            await ApiClient.Countries.create(data);
            UI.showSuccess('Страна создана');
        }

        UI.cancelCountryForm();
        UI.renderCountriesTable();
    } catch (error) {
        UI.showError('Не удалось сохранить: ' + error.message);
    }
};

UI.cancelCountryForm = function() {
    document.getElementById('country-form-container').innerHTML = '';
    document.getElementById('country-form-container').style.display = 'none';
};

UI.deleteCountry = async function(countryId) {
    if (!confirm('Удалить эту страну?')) return;
    try {
        await ApiClient.Countries.delete(countryId);
        UI.showSuccess('Страна удалена');
        UI.renderCountriesTable();
    } catch (error) {
        UI.showError(error.message);
    }
};

/**
 * Logout function
 */
async function logout() {
    if (!confirm('Вы уверены, что хотите выйти?')) {
        return;
    }

    try {
        console.log('🚪 Logging out...');

        const response = await fetch('/api/index.php?entity=auth&action=logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (response.ok) {
            console.log('Logout successful');
            UI.showSuccess('Выход выполнен! Перенаправление...');

            // Redirect to login page after 1 second
            setTimeout(() => {
                window.location.href = '/login.html';
            }, 1000);
        } else {
            console.error('Logout failed:', data.error);
            UI.showError('Ошибка выхода: ' + data.error);
        }
    } catch (error) {
        console.error('Logout error:', error);
        UI.showError('Ошибка выхода: ' + error.message);
    }
}
