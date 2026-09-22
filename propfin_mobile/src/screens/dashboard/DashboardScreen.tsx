import React, { useState, useEffect, useCallback, useRef } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  RefreshControl,
  Animated,
  Easing,
  StatusBar,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { MetricCard } from '../../components/MetricCard';
import { DashboardSkeleton } from '../../components/SkeletonLoader';
import { RootStackParamList } from '../../types';

interface LeadCountsData {
  total: number;
  open: number;
  progress: number;
  close: number;
}

interface TodayCountsData {
  today_total: number;
  today_open: number;
  today_pending: number;
  today_close: number;
}

let cachedLeadCounts: LeadCountsData | null = null;
let cachedTodayCounts: TodayCountsData | null = null;
let cachedIsCheckedIn: boolean | null = null;

const getGreeting = () => {
  const h = new Date().getHours();
  if (h < 12) return 'Good Morning';
  if (h < 17) return 'Good Afternoon';
  return 'Good Evening';
};

export const DashboardScreen = () => {
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const { authKey, userName } = useAuth();

  const [initialLoading, setInitialLoading] = useState(!cachedLeadCounts);
  const [refreshing, setRefreshing] = useState(false);
  const [isCheckedIn, setIsCheckedIn] = useState(cachedIsCheckedIn ?? false);
  const [leadCounts, setLeadCounts] = useState<LeadCountsData>(
    cachedLeadCounts ?? {
      total: 0,
      open: 0,
      progress: 0,
      close: 0,
    }
  );
  const [todayCounts, setTodayCounts] = useState<TodayCountsData>(
    cachedTodayCounts ?? {
      today_total: 0,
      today_open: 0,
      today_pending: 0,
      today_close: 0,
    }
  );

  const fabScale = useRef(new Animated.Value(1)).current;

  const loadDashboardData = useCallback(async () => {
    if (!authKey) return;
    try {
      const todayDate = new Date().toISOString().split('T')[0];

      // Concurrently fetch counts and attendance status in PARALLEL
      const [countRes, checkRes] = await Promise.all([
        ApiService.getLeadCount(authKey).catch((e) => {
          console.warn('Dashboard counts error', e);
          return null;
        }),
        ApiService.checkInStatus(authKey, todayDate).catch((e) => {
          console.warn('Check-in status error', e);
          return null;
        }),
      ]);

      // 1. Lead summary counts
      const recordList = countRes?.record_count || countRes?.recordCount || [];
      if (recordList.length > 0) {
        const item = recordList[0];
        const newLeadCounts = {
          total: Number(item.total ?? 0),
          open: Number(item.open ?? 0),
          progress: Number(item.Progress ?? item.progress ?? 0),
          close: Number(item.close ?? 0),
        };
        setLeadCounts(newLeadCounts);
        cachedLeadCounts = newLeadCounts;
      }

      // 2. Today's counts from the SAME single response
      if (countRes?.today_count && countRes.today_count.length > 0) {
        const t = countRes.today_count[0];
        const newTodayCounts = {
          today_total: Number(t.TodayTotal ?? t.today_total ?? 0),
          today_open: Number(t.TodayOpen ?? t.today_open ?? 0),
          today_pending: Number(t.TodayPending ?? t.today_pending ?? 0),
          today_close: Number(t.TodayClose ?? t.today_close ?? 0),
        };
        setTodayCounts(newTodayCounts);
        cachedTodayCounts = newTodayCounts;
      }

      // 3. Attendance Status
      if (checkRes && checkRes.data && checkRes.data.empcheckindata) {
        const list = checkRes.data.empcheckindata;
        if (list.length > 0) {
          const last = list[list.length - 1];
          const checked = !last.check_out_date || last.check_out_date === 'null';
          setIsCheckedIn(checked);
          cachedIsCheckedIn = checked;
        } else {
          setIsCheckedIn(false);
          cachedIsCheckedIn = false;
        }
      }
    } catch (e) {
      console.warn('Dashboard data fetch failed', e);
    } finally {
      setInitialLoading(false);
    }
  }, [authKey]);

  useEffect(() => {
    loadDashboardData();
  }, [loadDashboardData]);

  const onRefresh = async () => {
    setRefreshing(true);
    await loadDashboardData();
    setRefreshing(false);
  };

  const handleFabPress = () => {
    Animated.sequence([
      Animated.timing(fabScale, { toValue: 0.85, duration: 100, useNativeDriver: true }),
      Animated.timing(fabScale, { toValue: 1, duration: 150, useNativeDriver: true }),
    ]).start(() => {
      navigation.navigate('NewLead');
    });
  };

  const navigateToLeadList = (leadType: number, title: string) => {
    navigation.navigate('LeadList', { leadType, title });
  };

  const firstName = userName ? userName.split(' ')[0] : 'Agent';

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.background} barStyle="dark-content" />
      <ScrollView
        contentContainerStyle={styles.scrollContent}
        refreshControl={
          <RefreshControl
            refreshing={refreshing}
            onRefresh={onRefresh}
            colors={[Colors.primary]}
            tintColor={Colors.primary}
          />
        }
        showsVerticalScrollIndicator={false}
      >
        {initialLoading ? (
          <DashboardSkeleton />
        ) : (
          <>
            {/* Welcome Banner */}
            <View style={styles.welcomeBanner}>
              <View style={styles.welcomeTextWrap}>
                <Text style={styles.greetingLabel}>{getGreeting()}</Text>
                <Text style={styles.greetingText}>{firstName} 👋</Text>
                <Text style={styles.dateText}>
                  {new Date().toLocaleDateString('en-US', {
                    weekday: 'long',
                    month: 'short',
                    day: 'numeric',
                  })}
                </Text>
              </View>
              <View style={styles.statusWrap}>
                <View style={styles.statusDot} />
                <Text style={styles.statusText}>Active</Text>
              </View>
            </View>

            {/* Check In / Check Out Card */}
            <View style={[styles.attendanceCard, isCheckedIn && styles.attendanceCardIn]}>
              <View style={styles.attendanceInfo}>
                <View style={[styles.attendanceIconWrap, { backgroundColor: isCheckedIn ? '#ECFDF5' : '#FEF3C7' }]}>
                  <Ionicons
                    name={isCheckedIn ? 'checkmark-circle' : 'time-outline'}
                    size={26}
                    color={isCheckedIn ? Colors.success : '#D97706'}
                  />
                </View>
                <View style={styles.attendanceTextWrap}>
                  <Text style={styles.attendanceStatusTitle}>
                    {isCheckedIn ? 'Checked In ✓' : 'Not Checked In'}
                  </Text>
                  <Text style={styles.attendanceStatusSub}>
                    {isCheckedIn
                      ? 'Ready to check out when done'
                      : 'Tap to punch in your attendance'}
                  </Text>
                </View>
              </View>

              <TouchableOpacity
                style={[
                  styles.attendanceBtn,
                  { backgroundColor: isCheckedIn ? Colors.danger : Colors.success },
                ]}
                onPress={() =>
                  navigation.navigate('CheckInOut', {
                    type: isCheckedIn ? 'checkOut' : 'checkIn',
                  })
                }
                activeOpacity={0.85}
              >
                <Ionicons
                  name={isCheckedIn ? 'log-out-outline' : 'log-in-outline'}
                  size={14}
                  color={Colors.white}
                />
                <Text style={styles.attendanceBtnText}>
                  {isCheckedIn ? 'CHECK OUT' : 'CHECK IN'}
                </Text>
              </TouchableOpacity>
            </View>

            {/* Pending Follow-up Highlight Card */}
            <TouchableOpacity
              style={styles.followUpHighlightCard}
              onPress={() => navigateToLeadList(8, 'Pending Follow-Ups')}
              activeOpacity={0.85}
            >
              <View style={styles.followUpLeft}>
                <View style={styles.followUpIconWrap}>
                  <Ionicons name="notifications" size={22} color={Colors.primary} />
                </View>
                <View>
                  <Text style={styles.followUpCount}>
                    {todayCounts.today_pending || 0}
                  </Text>
                  <Text style={styles.followUpTitle}>Pending Follow-Ups Today</Text>
                </View>
              </View>
              <View style={styles.followUpArrow}>
                <Ionicons name="chevron-forward" size={18} color={Colors.primary} />
              </View>
            </TouchableOpacity>

            {/* Lead Summary Overview */}
            <Text style={styles.sectionHeader}>Overview</Text>
            <View style={styles.gridRow}>
              <MetricCard
                title="Total Leads"
                count={leadCounts.total}
                iconName="people"
                color="#E91E63"
                onPress={() => navigateToLeadList(3, 'Total Leads')}
              />
              <MetricCard
                title="Lead Open"
                count={leadCounts.open}
                iconName="folder-open"
                color="#2196F3"
                onPress={() => navigateToLeadList(1, 'Lead Open')}
              />
            </View>
            <View style={styles.gridRow}>
              <MetricCard
                title="In Process"
                count={leadCounts.progress}
                iconName="sync"
                color="#FF9800"
                onPress={() => navigateToLeadList(4, 'Lead In Process')}
              />
              <MetricCard
                title="Lead Closed"
                count={leadCounts.close}
                iconName="checkmark-done-circle"
                color="#4CAF50"
                onPress={() => navigateToLeadList(2, 'Lead Closed')}
              />
            </View>

            {/* Today's Activity */}
            <Text style={styles.sectionHeader}>Today's Activity</Text>
            <View style={styles.gridRow}>
              <MetricCard
                title="Today Total"
                count={todayCounts.today_total}
                iconName="calendar"
                color="#9C27B0"
                onPress={() => navigateToLeadList(6, "Today's Total Leads")}
              />
              <MetricCard
                title="Today Activity"
                count={todayCounts.today_open}
                iconName="pulse"
                color="#00BCD4"
                onPress={() => navigateToLeadList(7, "Today's Activity")}
              />
            </View>
            <View style={styles.gridRow}>
              <MetricCard
                title="Today Pending"
                count={todayCounts.today_pending}
                iconName="hourglass"
                color="#FF5722"
                onPress={() => navigateToLeadList(8, 'Today Pending')}
              />
              <MetricCard
                title="Today Closed"
                count={todayCounts.today_close}
                iconName="trophy"
                color="#4CAF50"
                onPress={() => navigateToLeadList(9, 'Today Closed')}
              />
            </View>
          </>
        )}
      </ScrollView>

      {/* Floating Action Button */}
      <Animated.View
        style={[
          styles.fab,
          { transform: [{ scale: fabScale }] },
        ]}
      >
        <TouchableOpacity
          style={styles.fabBtn}
          onPress={handleFabPress}
          activeOpacity={0.85}
        >
          <Ionicons name="add" size={28} color={Colors.white} />
        </TouchableOpacity>
      </Animated.View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  scrollContent: {
    padding: 14,
    paddingBottom: 90,
  },
  welcomeBanner: {
    backgroundColor: Colors.white,
    borderRadius: 18,
    padding: 18,
    marginBottom: 12,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 8,
    borderWidth: 1,
    borderColor: '#F1F5F9',
    borderLeftWidth: 4,
    borderLeftColor: Colors.primary,
  },
  welcomeTextWrap: {
    flex: 1,
  },
  greetingLabel: {
    fontSize: 11,
    fontWeight: '600',
    color: Colors.grey,
    textTransform: 'uppercase',
    letterSpacing: 0.8,
    marginBottom: 2,
  },
  greetingText: {
    fontSize: 20,
    fontWeight: '800',
    color: Colors.textDark,
    marginBottom: 2,
  },
  dateText: {
    fontSize: 12,
    color: Colors.grey,
    fontWeight: '500',
  },
  statusWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#ECFDF5',
    paddingHorizontal: 10,
    paddingVertical: 6,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: '#A7F3D0',
  },
  statusDot: {
    width: 7,
    height: 7,
    borderRadius: 3.5,
    backgroundColor: Colors.success,
    marginRight: 5,
  },
  statusText: {
    fontSize: 11,
    fontWeight: '700',
    color: '#059669',
  },
  attendanceCard: {
    backgroundColor: Colors.white,
    borderRadius: 18,
    padding: 14,
    marginBottom: 12,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  attendanceCardIn: {
    borderColor: '#D1FAE5',
    backgroundColor: '#F0FDF4',
  },
  attendanceInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  attendanceIconWrap: {
    width: 46,
    height: 46,
    borderRadius: 23,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  attendanceTextWrap: {
    flex: 1,
  },
  attendanceStatusTitle: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.textDark,
  },
  attendanceStatusSub: {
    fontSize: 12,
    color: Colors.grey,
    marginTop: 2,
    lineHeight: 16,
  },
  attendanceBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 10,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.15,
    shadowRadius: 2,
    gap: 4,
  },
  attendanceBtnText: {
    color: Colors.white,
    fontWeight: '800',
    fontSize: 11,
    letterSpacing: 0.5,
  },
  followUpHighlightCard: {
    backgroundColor: `${Colors.primary}10`,
    borderRadius: 18,
    padding: 16,
    marginBottom: 16,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    borderWidth: 1.5,
    borderColor: `${Colors.primary}30`,
  },
  followUpLeft: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  followUpIconWrap: {
    width: 48,
    height: 48,
    borderRadius: 24,
    backgroundColor: `${Colors.primary}18`,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 14,
    borderWidth: 1.5,
    borderColor: `${Colors.primary}25`,
  },
  followUpCount: {
    fontSize: 26,
    fontWeight: '800',
    color: Colors.primary,
  },
  followUpTitle: {
    fontSize: 12,
    color: Colors.primaryDark,
    fontWeight: '600',
  },
  followUpArrow: {
    width: 32,
    height: 32,
    borderRadius: 16,
    backgroundColor: `${Colors.primary}18`,
    justifyContent: 'center',
    alignItems: 'center',
  },
  sectionHeader: {
    fontSize: 15,
    fontWeight: '800',
    color: Colors.textDark,
    marginVertical: 10,
    marginLeft: 4,
    letterSpacing: 0.2,
  },
  gridRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 2,
  },
  fab: {
    position: 'absolute',
    bottom: 22,
    right: 20,
    elevation: 6,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.4,
    shadowRadius: 8,
  },
  fabBtn: {
    width: 56,
    height: 56,
    borderRadius: 28,
    backgroundColor: Colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
});
