import React, { createContext, useContext, useState, useEffect, ReactNode } from 'react';
import { Storage } from '../utils/storage';
import { ApiService } from '../api/services';

interface AuthContextType {
  authKey: string | null;
  userName: string | null;
  userMobile: string | null;
  isLoading: boolean;
  login: (mobile: string) => Promise<{ success: boolean; message: string }>;
  logout: () => Promise<void>;
  setSession: (key: string, name: string, mobile: string) => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [authKey, setAuthKey] = useState<string | null>(null);
  const [userName, setUserName] = useState<string | null>(null);
  const [userMobile, setUserMobile] = useState<string | null>(null);
  const [isLoading, setIsLoading] = useState<boolean>(true);

  useEffect(() => {
    loadSession();
  }, []);

  const loadSession = async () => {
    try {
      const storedKey = await Storage.getAuthKey();
      const storedName = await Storage.getUserName();
      const storedMobile = await Storage.getUserMobile();

      if (storedKey) {
        setAuthKey(storedKey);
        setUserName(storedName || 'User');
        setUserMobile(storedMobile);
      }
    } catch (e) {
      console.error('Failed to restore session', e);
    } finally {
      setIsLoading(false);
    }
  };

  const login = async (mobile: string): Promise<{ success: boolean; message: string }> => {
    try {
      const res = await ApiService.login(mobile);
      if (res && res.record && res.record.length > 0) {
        const item = res.record[0];
        if (item.status === 1 && item.key) {
          await Storage.setAuthKey(item.key);
          if (item.Name) {
            await Storage.setUserName(item.Name);
            setUserName(item.Name);
          }
          await Storage.setUserMobile(mobile);
          setAuthKey(item.key);
          setUserMobile(mobile);
          return { success: true, message: item.msg || 'Login successful' };
        } else {
          return { success: false, message: item.msg || 'Please enter registered mobile number.' };
        }
      }
      return { success: false, message: 'Server returned invalid response.' };
    } catch (error: any) {
      return { success: false, message: error?.message || 'Server connection error.' };
    }
  };

  const setSession = async (key: string, name: string, mobile: string) => {
    await Storage.setAuthKey(key);
    await Storage.setUserName(name);
    await Storage.setUserMobile(mobile);
    setAuthKey(key);
    setUserName(name);
    setUserMobile(mobile);
  };

  const logout = async () => {
    await Storage.clearSession();
    setAuthKey(null);
    setUserName(null);
    setUserMobile(null);
  };

  return (
    <AuthContext.Provider
      value={{
        authKey,
        userName,
        userMobile,
        isLoading,
        login,
        logout,
        setSession,
      }}
    >
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
