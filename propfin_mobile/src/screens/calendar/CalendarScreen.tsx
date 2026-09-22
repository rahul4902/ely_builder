import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  FlatList,
  ActivityIndicator,
  StatusBar,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { LeadListSkeleton } from '../../components/SkeletonLoader';
import { AppLinking } from '../../utils/linking';
import { CalendarEventItem } from '../../types';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { RootStackParamList } from '../../types';

export const CalendarScreen = () => {
  const { authKey } = useAuth();
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();

  const [currentWeekStart, setCurrentWeekStart] = useState<Date>(() => {
    const d = new Date();
    const day = d.getDay();
    const diff = d.getDate() - day + (day === 0 ? -6 : 1);
    return new Date(new Date().setDate(diff));
  });

  const [selectedDate, setSelectedDate] = useState<string>(
    new Date().toISOString().split('T')[0]
  );

  const [allEvents, setAllEvents] = useState<CalendarEventItem[]>([]);
  const [dayEvents, setDayEvents] = useState<CalendarEventItem[]>([]);
  const [loading, setLoading] = useState(true);

  const fetchFollowups = async () => {
    if (!authKey) return;
    try {
      const res = await ApiService.getCalendarFollowups(authKey);
      const events = res?.followup || res?.Userfollowups || [];
      if (Array.isArray(events)) {
        setAllEvents(events);
      }
    } catch (e) {
      console.warn('Calendar events fetch error', e);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchFollowups();
  }, [authKey]);

  useEffect(() => {
    const filtered = allEvents.filter((item) => {
      if (!item.follow_up_date) return false;
      return item.follow_up_date.startsWith(selectedDate);
    });
    setDayEvents(filtered);
  }, [selectedDate, allEvents]);

  const weekDays = Array.from({ length: 7 }, (_, i) => {
    const d = new Date(currentWeekStart);
    d.setDate(d.getDate() + i);
    const dateStr = d.toISOString().split('T')[0];
    const dayName = d.toLocaleDateString('en-US', { weekday: 'short' });
    const dayNum = d.getDate();
    const isToday = dateStr === new Date().toISOString().split('T')[0];
    const hasEvents = allEvents.some((e) => e.follow_up_date?.startsWith(dateStr));
    return { dateStr, dayName, dayNum, fullDate: d, isToday, hasEvents };
  });

  const handlePrevWeek = () => {
    const d = new Date(currentWeekStart);
    d.setDate(d.getDate() - 7);
    setCurrentWeekStart(new Date(d));
  };

  const handleNextWeek = () => {
    const d = new Date(currentWeekStart);
    d.setDate(d.getDate() + 7);
    setCurrentWeekStart(new Date(d));
  };

  const handleCall = async (item: CalendarEventItem) => {
    if (!item.contact_no) {
      Alert.alert('No Number', 'No phone number for this follow-up.');
      return;
    }
    await AppLinking.makePhoneCall(item.contact_no, item.lead_id, (time) => {
      const endTime = new Date(time.getTime() + 15000);
      if (authKey && item.lead_id) {
        ApiService.addCallLog({
          key: authKey,
          lead_id: item.lead_id,
          call_start_datetime: time.toISOString().replace('T', ' ').substring(0, 19),
          call_end_datetime: endTime.toISOString().replace('T', ' ').substring(0, 19),
        }).catch(() => {});
      }
    });
  };

  const monthYearLabel = currentWeekStart.toLocaleDateString('en-US', {
    month: 'long',
    year: 'numeric',
  });

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      
      {/* Month Header */}
      <View style={styles.headerRow}>
        <TouchableOpacity style={styles.weekNavBtn} onPress={handlePrevWeek} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
          <Ionicons name="chevron-back" size={20} color={Colors.white} />
        </TouchableOpacity>

        <View style={styles.monthTitleWrap}>
          <Text style={styles.monthTitle}>{monthYearLabel}</Text>
          <Text style={styles.followupCount}>
            {dayEvents.length > 0 ? `${dayEvents.length} follow-up${dayEvents.length > 1 ? 's' : ''}` : 'No events'}
          </Text>
        </View>

        <TouchableOpacity style={styles.weekNavBtn} onPress={handleNextWeek} hitSlop={{ top: 8, bottom: 8, left: 8, right: 8 }}>
          <Ionicons name="chevron-forward" size={20} color={Colors.white} />
        </TouchableOpacity>
      </View>

      {/* Week Day Bar */}
      <View style={styles.weekBar}>
        {weekDays.map((day) => {
          const isSelected = day.dateStr === selectedDate;
          return (
            <TouchableOpacity
              key={day.dateStr}
              style={[
                styles.dayItem,
                isSelected && styles.dayItemSelected,
                day.isToday && !isSelected && styles.dayItemToday,
              ]}
              onPress={() => setSelectedDate(day.dateStr)}
              activeOpacity={0.7}
            >
              <Text style={[styles.dayName, isSelected && styles.dayNameSelected, day.isToday && !isSelected && styles.dayNameToday]}>
                {day.dayName}
              </Text>
              <Text style={[styles.dayNum, isSelected && styles.dayNumSelected]}>
                {day.dayNum}
              </Text>
              {day.hasEvents && !isSelected && (
                <View style={styles.eventDot} />
              )}
            </TouchableOpacity>
          );
        })}
      </View>

      {/* Events List */}
      {loading ? (
        <LeadListSkeleton count={4} />
      ) : dayEvents.length === 0 ? (
        <View style={styles.centerContainer}>
          <View style={styles.emptyIconWrap}>
            <Ionicons name="calendar-outline" size={48} color={Colors.borderGrey} />
          </View>
          <Text style={styles.emptyTitle}>No Follow-Ups Scheduled</Text>
          <Text style={styles.emptySub}>
            Nothing scheduled for{' '}
            {new Date(selectedDate + 'T00:00:00').toLocaleDateString('en-US', {
              weekday: 'long',
              month: 'short',
              day: 'numeric',
            })}
            .
          </Text>
        </View>
      ) : (
        <FlatList
          data={dayEvents}
          keyExtractor={(item) => String(item.id || item.lead_id)}
          contentContainerStyle={styles.eventsList}
          showsVerticalScrollIndicator={false}
          renderItem={({ item }) => (
            <View style={styles.eventCard}>
              <View style={styles.eventTimeStrip}>
                <Ionicons name="time-outline" size={14} color={Colors.primary} />
                <Text style={styles.eventTimeText}>
                  {item.follow_up_date?.split(' ')[1] || '—'}
                </Text>
              </View>
              <View style={styles.eventInfo}>
                <Text style={styles.eventClientName} numberOfLines={1}>{item.name}</Text>
                {item.comment ? (
                  <Text style={styles.eventComment} numberOfLines={2}>
                    {item.comment}
                  </Text>
                ) : null}
              </View>

              <View style={styles.eventActions}>
                <TouchableOpacity
                  style={[styles.actionBtn, { backgroundColor: '#25D366' }]}
                  onPress={() => item.contact_no && AppLinking.openWhatsApp(item.contact_no)}
                  activeOpacity={0.8}
                >
                  <Ionicons name="logo-whatsapp" size={15} color={Colors.white} />
                </TouchableOpacity>
                <TouchableOpacity
                  style={[styles.actionBtn, { backgroundColor: '#0284C7' }]}
                  onPress={() => handleCall(item)}
                  activeOpacity={0.8}
                >
                  <Ionicons name="call" size={15} color={Colors.white} />
                </TouchableOpacity>
              </View>
            </View>
          )}
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
  headerRow: {
    backgroundColor: Colors.primary,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 14,
    paddingVertical: 12,
  },
  weekNavBtn: {
    width: 36,
    height: 36,
    borderRadius: 18,
    backgroundColor: 'rgba(255, 255, 255, 0.2)',
    justifyContent: 'center',
    alignItems: 'center',
  },
  monthTitleWrap: {
    alignItems: 'center',
  },
  monthTitle: {
    fontSize: 16,
    fontWeight: '800',
    color: Colors.white,
  },
  followupCount: {
    fontSize: 11,
    color: 'rgba(255,255,255,0.8)',
    fontWeight: '500',
    marginTop: 2,
  },
  weekBar: {
    flexDirection: 'row',
    backgroundColor: Colors.white,
    paddingVertical: 10,
    paddingHorizontal: 6,
    borderBottomWidth: 1,
    borderBottomColor: '#EAEAEA',
    justifyContent: 'space-around',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 3,
  },
  dayItem: {
    alignItems: 'center',
    paddingVertical: 8,
    paddingHorizontal: 9,
    borderRadius: 12,
    minWidth: 38,
  },
  dayItemSelected: {
    backgroundColor: Colors.primary,
  },
  dayItemToday: {
    backgroundColor: Colors.primaryLight,
  },
  dayName: {
    fontSize: 10,
    color: Colors.grey,
    fontWeight: '600',
    marginBottom: 4,
    textTransform: 'uppercase',
  },
  dayNameSelected: {
    color: Colors.white,
  },
  dayNameToday: {
    color: Colors.primary,
  },
  dayNum: {
    fontSize: 15,
    fontWeight: '800',
    color: Colors.textDark,
  },
  dayNumSelected: {
    color: Colors.white,
  },
  eventDot: {
    width: 5,
    height: 5,
    borderRadius: 2.5,
    backgroundColor: Colors.primary,
    marginTop: 3,
  },
  eventsList: {
    padding: 12,
    paddingBottom: 20,
  },
  eventCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 14,
    marginBottom: 10,
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#F1F5F9',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
  },
  eventTimeStrip: {
    alignItems: 'center',
    paddingRight: 12,
    borderRightWidth: 1,
    borderRightColor: '#F1F5F9',
    minWidth: 48,
  },
  eventTimeText: {
    fontSize: 11,
    color: Colors.primary,
    fontWeight: '700',
    marginTop: 3,
  },
  eventInfo: {
    flex: 1,
    paddingHorizontal: 12,
  },
  eventClientName: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textDark,
    marginBottom: 3,
  },
  eventComment: {
    fontSize: 12,
    color: Colors.grey,
    lineHeight: 17,
  },
  eventActions: {
    flexDirection: 'column',
    gap: 7,
  },
  actionBtn: {
    width: 34,
    height: 34,
    borderRadius: 17,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
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
