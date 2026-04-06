/**
 * TRUSTLINK - JavaScript API Client
 * Version: 1.0 | Production Ready | March 2026
 * 
 * Description: Centralized API client for all backend communication
 * Features:
 * - Automatic session cookie handling
 * - JSON request/response
 * - Error handling
 * - Loading state management
 */

const API = (function() {
    // Get the base path correctly
    const pathParts = window.location.pathname.split('/');
    const basePath = pathParts.slice(0, -1).join('/'); // Remove the current file
    
    // For API calls, we need to go up from public folder to root
    const BASE_URL = window.location.origin + '/trustfiles';
    
    console.log('API Base URL:', BASE_URL); // Debug log
    
    async function request(endpoint, options = {}) {
        const url = `${BASE_URL}/api${endpoint}`;
        console.log("MAIN API URL:", url); // Debug log
        
        const config = {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            credentials: 'same-origin',
            ...options
        };
        
        if (options.body && typeof options.body === 'object') {
            config.body = JSON.stringify(options.body);
        }
        
        try {
            const response = await fetch(url, config);
            const data = await response.json();
            
            console.log('API Response:', data); // Debug log
            
            if (!response.ok) {
                throw {
                    status: response.status,
                    message: data.message || 'An error occurred',
                    errors: data.errors
                };
            }
            
            return data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    }
    
    // ... rest of the API methods remain the same
    
    /**
     * GET request
     * @param {string} endpoint - API endpoint
     * @param {Object} params - Query parameters
     * @returns {Promise}
     */
    async function get(endpoint, params = {}) {
        const queryString = new URLSearchParams(params).toString();
        const url = queryString ? `${endpoint}?${queryString}` : endpoint;
        return request(url, { method: 'GET' });
    }
    
    /**
     * POST request
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request body
     * @returns {Promise}
     */
    async function post(endpoint, data = {}) {
        return request(endpoint, { method: 'POST', body: data });
    }
    
    /**
     * PUT request
     * @param {string} endpoint - API endpoint
     * @param {Object} data - Request body
     * @returns {Promise}
     */
    async function put(endpoint, data = {}) {
        return request(endpoint, { method: 'PUT', body: data });
    }
    
    /**
     * DELETE request
     * @param {string} endpoint - API endpoint
     * @returns {Promise}
     */
    async function del(endpoint) {
        return request(endpoint, { method: 'DELETE' });
    }
    
    /**
     * FormData POST (for file uploads)
     * @param {string} endpoint - API endpoint
     * @param {FormData} formData - Form data
     * @returns {Promise}
     */
    async function upload(endpoint, formData) {
        const url = `${BASE_URL}/api${endpoint}`;
        console.log("UPLOAD API URL:", url);
        
        try {
            const response = await fetch(url, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            });
                    const text = await response.text();
            console.log("RAW RESPONSE:", text);

            let data;
            try {
                data = JSON.parse(text);
            } catch (e) {
                throw new Error("Server returned invalid JSON:\n" + text);
            }
            
            if (!response.ok) {
                throw {
                    status: response.status,
                    message: data.message || 'Upload failed',
                    errors: data.errors
                };
            }
            
            return data;
        } catch (error) {
            console.error('Upload Error:', error);
            throw error;
        }
    }
    
    return {
        get,
        post,
        put,
        delete: del,
        upload
    };
})();

/**
 * Show toast notification
 * @param {string} message - Notification message
 * @param {string} type - success, error, warning, info
 */
function showToast(message, type = 'info') {
    // Create toast container if it doesn't exist
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <span class="toast-icon">${type === 'success' ? '✓' : type === 'error' ? '✗' : 'ℹ'}</span>
        <span class="toast-message">${escapeHtml(message)}</span>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.add('toast-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

/**
 * Escape HTML to prevent XSS
 * @param {string} str - String to escape
 * @returns {string} Escaped string
 */
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

/**
 * Format currency (KES)
 * @param {number} amount - Amount to format
 * @returns {string} Formatted amount
 */
function formatCurrency(amount) {
    return `KES ${Number(amount).toLocaleString()}`;
}

/**
 * Format date
 * @param {string} dateString - ISO date string
 * @returns {string} Formatted date
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-KE', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}

/**
 * Format time ago
 * @param {string} dateString - ISO date string
 * @returns {string} Time ago string
 */
function timeAgo(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diff = Math.floor((now - date) / 1000);
    
    if (diff < 60) return `${diff} seconds ago`;
    if (diff < 3600) return `${Math.floor(diff / 60)} minutes ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)} hours ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)} days ago`;
    if (diff < 2592000) return `${Math.floor(diff / 604800)} weeks ago`;
    return `${Math.floor(diff / 2592000)} months ago`;
}