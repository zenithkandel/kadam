/**
 * KADAM API Handler
 * Handles all communication with the PHP backend
 */

const API_CONFIG = {
    BASE_URL: 'http://localhost/projects/kadam/api',
    HEADERS: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    }
};

class ApiHandler {
    static async request(endpoint, method = 'GET', data = null) {
        const url = `${API_CONFIG.BASE_URL}/${endpoint}`;
        const headers = { ...API_CONFIG.HEADERS };
        
        // No token needed for sessions, browser handles cookies automatically
        const options = {
            method,
            headers,
            credentials: 'include' // Important for sending cookies (PHPSESSID)
        };

        if (data) {
            options.body = JSON.stringify(data);
        }

        try {
            const response = await fetch(url, options);
            
            // Handle 401 Unauthorized (Session expired)
            if (response.status === 401) {
                // Redirect to login if not already there
                if (!window.location.href.includes('login.html')) {
                    window.location.href = '/projects/kadam/login.html';
                }
                return;
            }

            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.message || 'API Request Failed');
            }

            return result;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }

    static async get(endpoint) {
        return this.request(endpoint, 'GET');
    }

    static async post(endpoint, data) {
        return this.request(endpoint, 'POST', data);
    }

    static async put(endpoint, data) {
        return this.request(endpoint, 'PUT', data);
    }

    static async delete(endpoint) {
        return this.request(endpoint, 'DELETE');
    }
}
