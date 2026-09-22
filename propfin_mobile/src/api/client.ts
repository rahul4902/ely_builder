import axios from 'axios';
import { Config } from '../constants/config';
import { Storage } from '../utils/storage';

const apiClient = axios.create({
  baseURL: Config.BASE_URL,
  timeout: 30000,
  headers: {
    'Content-Type': 'application/x-www-form-urlencoded',
    Accept: 'application/json',
  },
});

// Convert JS object to form-urlencoded body
export const toFormData = (data: Record<string, any>): string => {
  const params = new URLSearchParams();
  for (const key of Object.keys(data)) {
    if (data[key] !== undefined && data[key] !== null) {
      params.append(key, String(data[key]));
    }
  }
  return params.toString();
};

apiClient.interceptors.request.use(
  async (config) => {
    // If request data has no 'key', auto-inject the stored session key if available
    const token = await Storage.getAuthKey();
    if (token && config.data && typeof config.data === 'string') {
      const searchParams = new URLSearchParams(config.data);
      if (!searchParams.has('key')) {
        searchParams.append('key', token);
        config.data = searchParams.toString();
      }
    }
    return config;
  },
  (error) => Promise.reject(error)
);

apiClient.interceptors.response.use(
  (response) => response,
  (error) => {
    console.warn('API Error:', error?.response?.status, error?.message);
    return Promise.reject(error);
  }
);

export default apiClient;
