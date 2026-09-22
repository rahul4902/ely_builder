import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  Image,
  Alert,
  Platform,
  StatusBar,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/colors';
import { useAuth } from '../context/AuthContext';
import { ApiService } from '../api/services';

interface HeaderBarProps {
  title?: string;
  onNotificationPress?: () => void;
  showBack?: boolean;
  onBackPress?: () => void;
}

const STATUS_BAR_HEIGHT = Platform.OS === 'android' ? (StatusBar.currentHeight || 24) : 0;
const HEADER_HEIGHT = Platform.OS === 'ios' ? 50 : 56;

export const HeaderBar = ({
  title,
  onNotificationPress,
  showBack = false,
  onBackPress,
}: HeaderBarProps) => {
  const { authKey, logout } = useAuth();
  const [unreadCount, setUnreadCount] = useState<number>(0);

  useEffect(() => {
    if (authKey) {
      loadNotificationCount();
    }
  }, [authKey]);

  const loadNotificationCount = async () => {
    try {
      if (!authKey) return;
      const res = await ApiService.getUserNotifications(authKey);
      if (res && res.UserNotifications) {
        const unread = res.UserNotifications.filter(
          (n) => n.is_read === 0 || n.is_read === '0'
        ).length;
        setUnreadCount(unread || res.UserNotifications.length);
      }
    } catch {
      // Ignore count fetch errors
    }
  };

  const handleLogout = () => {
    Alert.alert(
      'Logout Confirmation',
      'Are you sure you want to logout?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Yes',
          style: 'destructive',
          onPress: async () => {
            await logout();
          },
        },
      ],
      { cancelable: true }
    );
  };

  return (
    <View style={styles.wrapper}>
      <StatusBar
        backgroundColor={Colors.primary}
        barStyle="light-content"
        translucent={false}
      />
      <View style={styles.container}>
        <View style={styles.leftRow}>
          {showBack ? (
            <TouchableOpacity onPress={onBackPress} style={styles.iconBtn} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="arrow-back" size={24} color={Colors.white} />
            </TouchableOpacity>
          ) : (
            <View style={styles.logoRow}>
              <Image
                source={require('../../assets/logo.png')}
                style={styles.logo}
                resizeMode="contain"
              />
            </View>
          )}
          {title ? (
            <Text style={styles.title} numberOfLines={1}>
              {title}
            </Text>
          ) : (
            <Text style={styles.appTitle}>ElyLeads</Text>
          )}
        </View>

        <View style={styles.rightRow}>
          {onNotificationPress && (
            <TouchableOpacity onPress={onNotificationPress} style={styles.iconBtn} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="notifications-outline" size={24} color={Colors.white} />
              {unreadCount > 0 && (
                <View style={styles.badge}>
                  <Text style={styles.badgeText}>
                    {unreadCount > 99 ? '99+' : unreadCount}
                  </Text>
                </View>
              )}
            </TouchableOpacity>
          )}

          {!showBack && (
            <TouchableOpacity onPress={handleLogout} style={styles.iconBtn} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
              <Ionicons name="log-out-outline" size={24} color={Colors.white} />
            </TouchableOpacity>
          )}
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  wrapper: {
    backgroundColor: Colors.primary,
    elevation: 4,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.2,
    shadowRadius: 3,
    zIndex: 100,
  },
  container: {
    height: HEADER_HEIGHT,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 16,
  },
  leftRow: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  logoRow: {
    marginRight: 8,
  },
  logo: {
    width: 32,
    height: 32,
    borderRadius: 6,
  },
  appTitle: {
    color: Colors.white,
    fontSize: 19,
    fontWeight: '800',
    letterSpacing: 0.6,
  },
  title: {
    color: Colors.white,
    fontSize: 17,
    fontWeight: '700',
    marginLeft: 10,
    flex: 1,
  },
  rightRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  iconBtn: {
    padding: 6,
    marginLeft: 4,
    position: 'relative',
  },
  badge: {
    position: 'absolute',
    top: 2,
    right: 2,
    backgroundColor: '#DC2626',
    borderRadius: 10,
    minWidth: 18,
    height: 18,
    justifyContent: 'center',
    alignItems: 'center',
    paddingHorizontal: 4,
    borderWidth: 1.5,
    borderColor: Colors.white,
  },
  badgeText: {
    color: Colors.white,
    fontSize: 9,
    fontWeight: '800',
  },
});
