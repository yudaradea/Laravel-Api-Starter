import axios from "axios";

const api = axios.create({
    baseURL: "http://backend-api-starter.test/api/v1",
    headers: {
        "Content-Type": "application/json",
        Accept: "application/json",
    },
    withCredentials: true, // Important for Sanctum cookie-based auth if used, but token based usually needs header
});

// Request interceptor to add token
api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token");
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});

export default api;
