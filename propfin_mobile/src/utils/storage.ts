import AsyncStorage from '@react-native-async-storage/async-storage';
import { Config } from '../constants/config';

export const Storage = {
  async setAuthKey(key: string): Promise<void> {
    await AsyncStorage.setItem(Config.STORAGE_KEYS.AUTH_KEY, key);
  },

  async getAuthKey(): Promise<string | null> {
    return await AsyncStorage.getItem(Config.STORAGE_KEYS.AUTH_KEY);
  },

  async setUserName(name: string): Promise<void> {
    await AsyncStorage.setItem(Config.STORAGE_KEYS.USER_NAME, name);
  },

  async getUserName(): Promise<string | null> {
    return await AsyncStorage.getItem(Config.STORAGE_KEYS.USER_NAME);
  },

  async setUserMobile(mobile: string): Promise<void> {
    await AsyncStorage.setItem(Config.STORAGE_KEYS.USER_MOBILE, mobile);
  },

  async getUserMobile(): Promise<string | null> {
    return await AsyncStorage.getItem(Config.STORAGE_KEYS.USER_MOBILE);
  },

  async setNotificationSound(enabled: boolean): Promise<void> {
    await AsyncStorage.setItem(Config.STORAGE_KEYS.NOTIFICATION_SOUND, JSON.stringify(enabled));
  },

  async getNotificationSound(): Promise<boolean> {
    const val = await AsyncStorage.getItem(Config.STORAGE_KEYS.NOTIFICATION_SOUND);
    return val !== null ? JSON.parse(val) : true;
  },

  async clearSession(): Promise<void> {
    await AsyncStorage.multiRemove([
      Config.STORAGE_KEYS.AUTH_KEY,
      Config.STORAGE_KEYS.USER_NAME,
      Config.STORAGE_KEYS.USER_MOBILE,
    ]);
  },
};
