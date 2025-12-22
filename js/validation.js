/**
 * Inline Field Validation
 * Validates fields on blur and displays real-time error messages
 */

const Validation = {
    /**
     * Check if field is required and not empty
     */
    required(value) {
        return value && value.trim().length > 0;
    },

    /**
     * Check if value is a positive number
     */
    positiveNumber(value) {
        const num = parseFloat(value);
        return !isNaN(num) && num > 0;
    },

    /**
     * Check if value is a valid date (YYYY-MM-DD)
     */
    validDate(value) {
        const regex = /^\d{4}-\d{2}-\d{2}$/;
        if (!regex.test(value)) return false;
        const date = new Date(value);
        return date instanceof Date && !isNaN(date);
    },

    /**
     * Check maximum length
     */
    maxLength(value, max) {
        return value.length <= max;
    },

    /**
     * Validate tour field
     */
    validateTourField(field, value) {
        let error = null;

        if (field === 'title') {
            if (!this.required(value)) {
                error = 'Требуется название тура';
            } else if (!this.maxLength(value, 200)) {
                error = 'Название тура не должно превышать 200 символов';
            }
        } else if (field === 'country_id') {
            if (!value) {
                error = 'Требуется выбрать страну';
            }
        } else if (field === 'start_date') {
            if (!this.required(value)) {
                error = 'Требуется дата начала';
            } else if (!this.validDate(value)) {
                error = 'Дата начала должна быть в формате ГГГГ-ММ-ДД';
            }
        } else if (field === 'price') {
            if (!this.required(value)) {
                error = 'Требуется цена';
            } else if (!this.positiveNumber(value)) {
                error = 'Цена должна быть положительным числом';
            }
        }

        return error;
    },

    /**
     * Validate client field
     */
    validateClientField(field, value) {
        let error = null;

        if (field === 'full_name') {
            if (!this.required(value)) {
                error = 'Требуется ФИО';
            } else if (!this.maxLength(value, 200)) {
                error = 'ФИО не должно превышать 200 символов';
            }
        } else if (field === 'phone') {
            if (!this.required(value)) {
                error = 'Требуется телефон';
            } else if (!this.maxLength(value, 20)) {
                error = 'Телефон не должен превышать 20 символов';
            }
        } else if (field === 'passport_data') {
            if (!this.required(value)) {
                error = 'Требуются паспортные данные';
            } else if (!this.maxLength(value, 100)) {
                error = 'Паспортные данные не должны превышать 100 символов';
            }
        }

        return error;
    },

    /**
     * Validate country field
     */
    validateCountryField(field, value) {
        let error = null;

        if (field === 'name') {
            if (!this.required(value)) {
                error = 'Требуется название страны';
            } else if (!this.maxLength(value, 100)) {
                error = 'Название страны не должно превышать 100 символов';
            }
        }

        return error;
    },

    /**
     * Show validation error for a field
     */
    showError(fieldId, message) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        field.classList.add('is-invalid');
        console.warn(`Validation Error [${fieldId}]: ${message}`);

        let errorEl = document.getElementById(fieldId + '-error');
        if (!errorEl) {
            errorEl = document.createElement('div');
            errorEl.id = fieldId + '-error';
            errorEl.className = 'error-message show';
            field.parentNode.appendChild(errorEl);
        } else {
            errorEl.classList.add('show');
        }

        errorEl.textContent = message;
    },

    /**
     * Clear validation error for a field
     */
    clearError(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;

        field.classList.remove('is-invalid');

        const errorEl = document.getElementById(fieldId + '-error');
        if (errorEl) {
            errorEl.classList.remove('show');
            errorEl.textContent = '';
        }

        console.log(`Validation cleared [${fieldId}]`);
    },

    /**
     * Attach blur event listeners to form fields
     */
    attachBlurListeners(formId, validateFunction) {
        const form = document.getElementById(formId);
        if (!form) return;

        form.querySelectorAll('input, textarea, select').forEach(field => {
            field.addEventListener('blur', (e) => {
                const fieldId = e.target.id;
                const error = validateFunction(fieldId, e.target.value);

                if (error) {
                    this.showError(fieldId, error);
                } else {
                    this.clearError(fieldId);
                }
            });
        });
    },

    /**
     * Validate entire form
     */
    validateForm(formId, validateFunction) {
        const form = document.getElementById(formId);
        if (!form) return true;

        let isValid = true;
        form.querySelectorAll('input, textarea, select').forEach(field => {
            const error = validateFunction(field.id, field.value);
            if (error) {
                this.showError(field.id, error);
                isValid = false;
            } else {
                this.clearError(field.id);
            }
        });

        return isValid;
    }
};
