import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  StatusBar,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { HeaderBar } from '../../components/HeaderBar';
import { LeadListSkeleton } from '../../components/SkeletonLoader';
import { UserNotificationItem, RootStackParamList } from '../../types';

export const NotificationScreen = () => {
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const { authKey } = useAuth();

  const [notifications, setNotifications] = useState<UserNotificationItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchNotifications = useCallback(async () => {
    if (!authKey) return;
    try {
      // 1. Fetch user notifications
      const res = await ApiService.getUserNotifications(authKey);
      if (res && res.UserNotifications) {
        setNotifications(res.UserNotifications);
      } else {
        setNotifications([]);
      }

      // 2. Mark notifications as read
      ApiService.markNotificationRead(authKey).catch(() => {});
    } catch (e) {
      console.warn('Failed to load notifications', e);
    } finally {
      setLoading(false);
    }
  }, [authKey]);

  useEffect(() => {
    fetchNotifications();
  }, [fetchNotifications]);

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchNotifications();
    setRefreshing(false);
  };

  const handleNotificationPress = (item: UserNotificationItem) => {
    if (item.lead_id) {
      navigation.navigate('LeadDetail', { leadId: item.lead_id });
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      <HeaderBar
        title="Notifications"
        showBack
        onBackPress={() => navigation.goBack()}
      />

      {loading ? (
        <LeadListSkeleton count={5} />
      ) : notifications.length === 0 ? (
        <View style={styles.centerContainer}>
          <View style={styles.emptyIconWrap}>
            <Ionicons name="notifications-off-outline" size={48} color={Colors.borderGrey} />
          </View>
          <Text style={styles.emptyTitle}>No Notifications</Text>
          <Text style={styles.emptySub}>
            You're all caught up! No new notifications at this time.
          </Text>
        </View>
      ) : (
        <FlatList
          data={notifications}
          keyExtractor={(item, index) => String(item.id || index)}
          contentContainerStyle={styles.listContent}
          renderItem={({ item }) => (
            <TouchableOpacity
              style={styles.notifCard}
              onPress={() => handleNotificationPress(item)}
              activeOpacity={item.lead_id ? 0.7 : 1}
            >
              <View style={styles.notifIconWrap}>
                <Ionicons
                  name="notifications"
                  size={20}
                  color={Colors.primary}
                />
              </View>

              <View style={styles.notifContent}>
                <Text style={styles.notifTitle}>{item.title || 'Notification'}</Text>
                <Text style={styles.notifMessage}>{item.message}</Text>
                <Text style={styles.notifTime}>{item.created_at}</Text>
              </View>

              {item.lead_id && (
                <Ionicons name="chevron-forward" size={18} color={Colors.grey} />
              )}
            </TouchableOpacity>
          )}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              colors={[Colors.primary]}
            />
          }
        />
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  listContent: {
    padding: 12,
  },
  notifCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    marginBottom: 10,
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#F1F5F9',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
  },
  notifIconWrap: {
    width: 40,
    height: 40,
    borderRadius: 20,
    backgroundColor: `${Colors.primary}15`,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  notifContent: {
    flex: 1,
  },
  notifTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.black,
  },
  notifMessage: {
    fontSize: 13,
    color: '#374151',
    marginTop: 3,
    lineHeight: 18,
  },
  notifTime: {
    fontSize: 11,
    color: '#9CA3AF',
    marginTop: 5,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 30,
  },
  emptyIconWrap: {
    width: 90,
    height: 90,
    borderRadius: 45,
    backgroundColor: Colors.lightGrey,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 16,
  },
  emptyTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textDark,
    marginBottom: 8,
  },
  emptySub: {
    fontSize: 13,
    color: Colors.grey,
    textAlign: 'center',
    lineHeight: 20,
  },
});
