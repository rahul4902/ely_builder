import { Platform } from 'react-native';

// Local web backend (propfincrm) with full CORS support
const LOCAL_BACKEND = Platform.OS === 'web' ? 'http://localhost:8000/' : 'http://10.79.48.233:8000/';

export const Config = {
  APP_NAME: 'ElyLeads',
  // Set to LOCAL_BACKEND for local dev server (CORS-free), or 'https://propfin.in/' for remote production
  BASE_URL: LOCAL_BACKEND,
  PRODUCTION_URL: 'https://propfin.in/',
  API_PREFIX: 'API',
  STORAGE_KEYS: {
    AUTH_KEY: '@elyleads_auth_key',
    USER_NAME: '@elyleads_user_name',
    USER_MOBILE: '@elyleads_user_mobile',
    LAST_LEAD_TYPE: '@elyleads_last_lead_type',
    NOTIFICATION_SOUND: '@elyleads_notification_sound',
    NOTIFICATION_VOLUME: '@elyleads_notification_volume',
  },
};
