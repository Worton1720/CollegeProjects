/**
 * API Client
 * Wrapper for fetch() calls to REST API
 */

const ApiClient = {
    BASE_URL: '/api/index.php',

    /**
     * Make API request with detailed logging
     */
    request: async (entity, action, method = 'GET', data = null, params = {}) => {
        const requestId = Date.now();
        const startTime = performance.now();

        try {
            let url = `${ApiClient.BASE_URL}?entity=${entity}&action=${action}`;

            // Add additional parameters
            for (const [key, value] of Object.entries(params)) {
                url += `&${key}=${encodeURIComponent(value)}`;
            }

            const options = {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            };

            if (data && (method === 'POST' || method === 'PUT')) {
                options.body = JSON.stringify(data);
            }

            // Log request
            console.group(`API Request #${requestId}`);
            console.log(`Method: ${method}`);
            console.log(`URL: ${url}`);
            if (data) {
                console.log('Data:', data);
            }
            console.groupEnd();

            const response = await fetch(url, options);
            const duration = (performance.now() - startTime).toFixed(2);

            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                const errorMsg = `Сервер вернул не-JSON ответ: ${text.substring(0, 200)}`;
                console.error(`Request #${requestId} - ${response.status} - ${duration}ms`);
                console.error('Response:', text.substring(0, 500));
                throw new Error(errorMsg);
            }

            const result = await response.json();

            if (!response.ok) {
                const errorMsg = result.error || 'Ошибка API запроса';
                console.group(`Request #${requestId} - HTTP ${response.status} - ${duration}ms`);
                console.error('Error:', errorMsg);
                if (result.errors) {
                    console.error('Validation Errors:', result.errors);
                }
                console.groupEnd();
                throw new Error(errorMsg);
            }

            // Log success response
            console.group(`Request #${requestId} - HTTP ${response.status} - ${duration}ms`);
            console.log('Response:', result.data);
            console.groupEnd();

            return result.data;
        } catch (error) {
            const duration = (performance.now() - startTime).toFixed(2);
            console.error(`Request #${requestId} failed (${duration}ms):`, error.message);
            throw error;
        }
    },

    /**
     * Countries API
     */
    Countries: {
        getAll: () => ApiClient.request('countries', 'list', 'GET'),
        create: (data) => ApiClient.request('countries', 'create', 'POST', data),
        update: (id, data) => ApiClient.request('countries', 'update', 'PUT', data, { id }),
        delete: (id) => ApiClient.request('countries', 'delete', 'DELETE', null, { id })
    },

    /**
     * Clients API
     */
    Clients: {
        getAll: () => ApiClient.request('clients', 'list', 'GET'),
        create: (data) => ApiClient.request('clients', 'create', 'POST', data),
        quickAdd: (data) => ApiClient.request('clients', 'quick-add', 'POST', data),
        update: (id, data) => ApiClient.request('clients', 'update', 'PUT', data, { id }),
        delete: (id) => ApiClient.request('clients', 'delete', 'DELETE', null, { id })
    },

    /**
     * Tours API
     */
    Tours: {
        getAll: (countryId = null) => {
            const params = countryId ? { country_id: countryId } : {};
            return ApiClient.request('tours', 'list', 'GET', null, params);
        },
        create: (data) => ApiClient.request('tours', 'create', 'POST', data),
        update: (id, data) => ApiClient.request('tours', 'update', 'PUT', data, { id }),
        delete: (id) => ApiClient.request('tours', 'delete', 'DELETE', null, { id })
    }
};
