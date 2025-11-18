// API Configuration for Laravel routes
// Detect if we're using Laravel dev server (port 8000) or XAMPP/Apache
const isDevServer = window.location.port === '8000' || window.location.hostname === '127.0.0.1';
const API_BASE_URL = '/api';

// API endpoints
const API_ENDPOINTS = {
    // Auth
    login: `${API_BASE_URL}/auth/login`,
    register: `${API_BASE_URL}/auth/register`,
    logout: `${API_BASE_URL}/auth/logout`,
    me: `${API_BASE_URL}/auth/me`,
    
    // Forum
    forums: `${API_BASE_URL}/forum/forums`,
    forumDetails: (id) => `${API_BASE_URL}/forum/forums/${id}`,
    createForum: `${API_BASE_URL}/forum/forums`,
    joinForum: `${API_BASE_URL}/forum/forums/join`,
    posts: `${API_BASE_URL}/forum/posts`,
    createPost: `${API_BASE_URL}/forum/posts`,
    comments: `${API_BASE_URL}/forum/comments`,
    createComment: `${API_BASE_URL}/forum/comments`,
    
    // Messaging
    conversations: `${API_BASE_URL}/messaging/conversations`,
    messages: `${API_BASE_URL}/messaging/messages`,
    sendMessage: `${API_BASE_URL}/messaging/messages`,
    createGroupChat: `${API_BASE_URL}/messaging/conversations/group`,
    
    // Notifications
    notifications: `${API_BASE_URL}/notifications`,
    markNotificationRead: (id) => `${API_BASE_URL}/notifications/${id}/read`,
    markAllNotificationsRead: `${API_BASE_URL}/notifications/read-all`,
    
    // Upload
    upload: `${API_BASE_URL}/upload`,
    
    // Legacy endpoints (for backward compatibility)
    forumEndpoints: `${API_BASE_URL}/forum_endpoints.php`,
    messagingEndpoints: `${API_BASE_URL}/messaging_endpoints.php`,
    notificationEndpoints: `${API_BASE_URL}/notification_endpoints.php`,
    uploadEndpoint: `${API_BASE_URL}/upload_endpoint.php`,
    loginLegacy: `${API_BASE_URL}/auth/login.php`,
    registerLegacy: `${API_BASE_URL}/auth/register.php`,
};

// Helper function to make API calls
async function apiCall(endpoint, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        },
        credentials: 'include',
    };
    
    const mergedOptions = {
        ...defaultOptions,
        ...options,
        headers: {
            ...defaultOptions.headers,
            ...(options.headers || {}),
        },
    };
    
    try {
        const response = await fetch(endpoint, mergedOptions);
        
        // Parse JSON response (even for error statuses, API returns JSON)
        let data;
        try {
            const text = await response.text();
            if (text) {
                data = JSON.parse(text);
            } else {
                data = {
                    status: response.status,
                    message: response.statusText || 'Empty response'
                };
            }
        } catch (e) {
            // If not JSON, create error object from response
            data = {
                status: response.status,
                message: response.statusText || 'Invalid response format'
            };
        }
        
        // Check if the API response indicates an error
        // For login/register endpoints, we check JSON status field, not just HTTP status
        // This is because Laravel might return HTTP 200 even for validation errors
        
        // If HTTP status is not ok (4xx, 5xx), it's definitely an error
        if (!response.ok) {
            const error = new Error(data.message || 'API request failed');
            error.response = response;
            error.data = data;
            error.status = data.status || response.status;
            throw error;
        }
        
        // If HTTP status is ok but JSON status indicates error, also treat as error
        // Exception: For /auth/me endpoint, status 401 in JSON is expected when not logged in
        if (data.status && data.status !== 200 && endpoint.includes('/auth/me') === false) {
            const error = new Error(data.message || 'API request failed');
            error.response = response;
            error.data = data;
            error.status = data.status;
            throw error;
        }
        
        return { response, data };
    } catch (error) {
        // If it's already our custom error, rethrow it
        if (error.response && error.data) {
            console.error('API call error:', {
                endpoint,
                error: error.message,
                status: error.status || error.response.status,
                data: error.data
            });
            throw error;
        }
        
        // Network or other errors (like connection refused)
        console.error('API call error:', {
            endpoint,
            error: error.message,
            currentUrl: window.location.href
        });
        
        // Provide helpful error message
        let errorMessage = 'Tidak dapat berhubung dengan pelayan.';
        if (error.message.includes('Failed to fetch') || error.message.includes('ERR_CONNECTION_REFUSED')) {
            errorMessage = 'Pelayan tidak dapat dicapai. Sila pastikan server Laravel sedang berjalan. ';
            if (window.location.port === '8000' || window.location.hostname === '127.0.0.1') {
                errorMessage += 'Jika menggunakan Laravel dev server, pastikan "php artisan serve" sedang berjalan.';
            } else {
                errorMessage += 'Jika menggunakan XAMPP, pastikan Apache sedang berjalan dan akses aplikasi melalui http://localhost/CompuPlay/public/';
            }
        }
        
        // Create a structured error
        const apiError = new Error(error.message || 'Network error or API unavailable');
        apiError.response = null;
        apiError.data = {
            status: 0,
            message: errorMessage
        };
        apiError.status = 0;
        throw apiError;
    }
}

// Helper function for GET requests
async function apiGet(endpoint, params = {}) {
    const queryString = new URLSearchParams(params).toString();
    const url = queryString ? `${endpoint}?${queryString}` : endpoint;
    return apiCall(url, { method: 'GET' });
}

// Helper function for POST requests
async function apiPost(endpoint, data = {}) {
    return apiCall(endpoint, {
        method: 'POST',
        body: JSON.stringify(data),
    });
}

// Helper function for PUT requests
async function apiPut(endpoint, data = {}) {
    return apiCall(endpoint, {
        method: 'PUT',
        body: JSON.stringify(data),
    });
}

// Helper function for DELETE requests
async function apiDelete(endpoint) {
    return apiCall(endpoint, { method: 'DELETE' });
}

