/**
 * UI Rendering Functions
 * Handles DOM manipulation and table/form rendering
 */

const UI = {
    /**
     * Render tours table with filtering
     */
    renderToursTable: async (countryId = null) => {
        try {
            const contentDiv = document.getElementById('content');
            contentDiv.innerHTML = '<p>Загрузка туров...</p>';

            // Fetch data
            const tours = await ApiClient.Tours.getAll(countryId);
            const countries = await ApiClient.Countries.getAll();

            // Build filter HTML
            let html = `
                <div class="filter-section">
                    <label class="form-label" for="country-filter">Фильтр по стране:</label>
                    <select id="country-filter" class="form-select">
                        <option value="">Все страны</option>
            `;

            countries.forEach(country => {
                html += `<option value="${country.id}">${country.name}</option>`;
            });

            html += `
                    </select>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h2>Туры</h2>
                    <button class="btn btn-primary" onclick="UI.showTourForm()">+ Добавить тур</button>
                </div>

                <div id="tour-form-container" style="display: none;"></div>
            `;

            if (tours.length === 0) {
                html += '<div class="empty-message"><h5>Туров нет</h5><p>Создайте первый тур для начала работы</p></div>';
            } else {
                html += `
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Название</th>
                                <th>Страна</th>
                                <th>Дата начала</th>
                                <th>Цена</th>
                                <th>Назначенный клиент</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                tours.forEach(tour => {
                    const assignedClient = tour.client_name || 'Не назначен';
                    html += `
                        <tr>
                            <td>${tour.title}</td>
                            <td>${tour.country_name}</td>
                            <td>${tour.start_date}</td>
                            <td>$${parseFloat(tour.price).toFixed(2)}</td>
                            <td>${assignedClient}</td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick="UI.showTourForm(${tour.id})">Редактировать</button>
                                <button class="btn btn-sm btn-danger" onclick="UI.deleteTour(${tour.id})">Удалить</button>
                                ${!tour.client_id ? `<button class="btn btn-sm btn-info" onclick="UI.showQuickAddForm(${tour.id})">Назначить клиента</button>` : ''}
                            </td>
                        </tr>
                    `;
                });

                html += '</tbody></table>';
            }

            contentDiv.innerHTML = html;

            // Attach filter listener
            document.getElementById('country-filter').addEventListener('change', (e) => {
                UI.renderToursTable(e.target.value || null);
            });
        } catch (error) {
            UI.showError('Не удалось загрузить туры: ' + error.message);
        }
    },

    /**
     * Show tour form
     */
    showTourForm: async (tourId = null) => {
        try {
            const countries = await ApiClient.Countries.getAll();
            const formContainer = document.getElementById('tour-form-container');

            let html = `
                <div class="card mb-4">
                    <div class="card-header">${tourId ? 'Редактирование тура' : 'Добавить новый тур'}</div>
                    <div class="card-body">
                        <form id="tour-form">
                            <div class="form-group">
                                <label class="form-label" for="tour-title">Название</label>
                                <input type="text" id="tour-title" class="form-control" placeholder="Название тура">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tour-country">Страна</label>
                                <select id="tour-country" class="form-select">
                                    <option value="">Выберите страну</option>
            `;

            countries.forEach(country => {
                html += `<option value="${country.id}">${country.name}</option>`;
            });

            html += `
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tour-date">Дата начала (ГГГГ-ММ-ДД)</label>
                                <input type="date" id="tour-date" class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="tour-price">Цена</label>
                                <input type="number" id="tour-price" class="form-control" step="0.01" placeholder="0.00">
                            </div>

                            <button type="button" class="btn btn-primary" onclick="UI.saveTour(${tourId || 'null'})">Сохранить</button>
                            <button type="button" class="btn btn-secondary" onclick="UI.cancelTourForm()">Отмена</button>
                        </form>
                    </div>
                </div>
            `;

            formContainer.innerHTML = html;
            formContainer.style.display = 'block';

            // Attach validation listeners
            Validation.attachBlurListeners('tour-form', (fieldId, value) => {
                return Validation.validateTourField(fieldId.replace('tour-', ''), value);
            });

            // Load existing tour data if editing
            if (tourId) {
                // Note: In a real implementation, we'd fetch the tour data
                // For MVP, we're keeping it simple
            }
        } catch (error) {
            UI.showError('Failed to load form: ' + error.message);
        }
    },

    /**
     * Save tour
     */
    saveTour: async (tourId) => {
        try {
            const isValid = Validation.validateForm('tour-form', (fieldId, value) => {
                const fieldName = fieldId.replace('tour-', '');
                return Validation.validateTourField(fieldName, value);
            });

            if (!isValid) return;

            const data = {
                title: document.getElementById('tour-title').value,
                country_id: parseInt(document.getElementById('tour-country').value),
                start_date: document.getElementById('tour-date').value,
                price: parseFloat(document.getElementById('tour-price').value)
            };

            if (tourId) {
                await ApiClient.Tours.update(tourId, data);
                UI.showSuccess('Tour updated successfully');
            } else {
                await ApiClient.Tours.create(data);
                UI.showSuccess('Tour created successfully');
            }

            UI.cancelTourForm();
            UI.renderToursTable();
        } catch (error) {
            UI.showError('Failed to save tour: ' + error.message);
        }
    },

    /**
     * Cancel tour form
     */
    cancelTourForm: () => {
        document.getElementById('tour-form-container').innerHTML = '';
        document.getElementById('tour-form-container').style.display = 'none';
    },

    /**
     * Delete tour
     */
    deleteTour: async (tourId) => {
        if (!confirm('Вы уверены, что хотите удалить этот тур?')) return;

        try {
            await ApiClient.Tours.delete(tourId);
            UI.showSuccess('Тур успешно удален');
            UI.renderToursTable();
        } catch (error) {
            UI.showError('Не удалось удалить тур: ' + error.message);
        }
    },

    /**
     * Show quick-add client form
     */
    showQuickAddForm: async (tourId) => {
        try {
            const formContainer = document.getElementById('tour-form-container');

            const html = `
                <div class="card mb-4">
                    <div class="card-header">Назначить нового клиента на тур</div>
                    <div class="card-body">
                        <form id="quickadd-form">
                            <div class="form-group">
                                <label class="form-label" for="qa-full-name">ФИО</label>
                                <input type="text" id="qa-full-name" class="form-control" placeholder="ФИО клиента">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="qa-phone">Телефон</label>
                                <input type="text" id="qa-phone" class="form-control" placeholder="Номер телефона">
                            </div>

                            <div class="form-group">
                                <label class="form-label" for="qa-passport">Паспортные данные</label>
                                <input type="text" id="qa-passport" class="form-control" placeholder="Паспортная информация">
                            </div>

                            <button type="button" class="btn btn-success" onclick="UI.saveQuickAdd(${tourId})">Создать и назначить</button>
                            <button type="button" class="btn btn-secondary" onclick="UI.cancelTourForm()">Отмена</button>
                        </form>
                    </div>
                </div>
            `;

            formContainer.innerHTML = html;
            formContainer.style.display = 'block';

            // Attach validation
            Validation.attachBlurListeners('quickadd-form', (fieldId, value) => {
                const fieldName = fieldId.replace('qa-', '');
                return Validation.validateClientField(fieldName, value);
            });
        } catch (error) {
            UI.showError('Не удалось загрузить форму: ' + error.message);
        }
    },

    /**
     * Save quick-add
     */
    saveQuickAdd: async (tourId) => {
        try {
            const isValid = Validation.validateForm('quickadd-form', (fieldId, value) => {
                const fieldName = fieldId.replace('qa-', '');
                return Validation.validateClientField(fieldName, value);
            });

            if (!isValid) return;

            const data = {
                full_name: document.getElementById('qa-full-name').value,
                phone: document.getElementById('qa-phone').value,
                passport_data: document.getElementById('qa-passport').value,
                tour_id: tourId
            };

            await ApiClient.Clients.quickAdd(data);
            UI.showSuccess('Клиент создан и назначен успешно');
            UI.cancelTourForm();
            UI.renderToursTable();
        } catch (error) {
            UI.showError('Не удалось создать и назначить клиента: ' + error.message);
        }
    },

    /**
     * Show error message
     */
    showError: (message) => {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.getElementById('content').insertBefore(alertDiv, document.getElementById('content').firstChild);
    },

    /**
     * Show success message
     */
    showSuccess: (message) => {
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-success alert-dismissible fade show';
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.getElementById('content').insertBefore(alertDiv, document.getElementById('content').firstChild);

        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
};
