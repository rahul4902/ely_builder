import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  FlatList,
  TouchableOpacity,
  RefreshControl,
  StatusBar,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { LeadListSkeleton } from '../../components/SkeletonLoader';
import { AppLinking } from '../../utils/linking';
import { CallLogItem } from '../../types';

export const CallLogsScreen = () => {
  const { authKey } = useAuth();

  const [logs, setLogs] = useState<CallLogItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [refreshing, setRefreshing] = useState(false);

  const fetchCallLogs = useCallback(async () => {
    if (!authKey) return;
    try {
      const res = await ApiService.getCallLogs(authKey);
      if (res && res.calllogs) {
        setLogs(res.calllogs);
      } else {
        setLogs([]);
      }
    } catch (e) {
      console.warn('Call logs fetch failed', e);
    } finally {
      setLoading(false);
    }
  }, [authKey]);

  useEffect(() => {
    fetchCallLogs();
  }, [fetchCallLogs]);

  const onRefresh = async () => {
    setRefreshing(true);
    await fetchCallLogs();
    setRefreshing(false);
  };

  const handleRedial = async (log: CallLogItem) => {
    if (!log.contact_no) {
      Alert.alert('No Number', 'No phone number for this call record.');
      return;
    }

    await AppLinking.makePhoneCall(log.contact_no, log.lead_id, (time) => {
      const endTime = new Date(time.getTime() + 15000);
      if (authKey && log.lead_id) {
        ApiService.addCallLog({
          key: authKey,
          lead_id: log.lead_id,
          call_start_datetime: time.toISOString().replace('T', ' ').substring(0, 19),
          call_end_datetime: endTime.toISOString().replace('T', ' ').substring(0, 19),
        }).catch(() => {});
      }
    });
  };

  const formatDuration = (startStr: string, endStr: string): string => {
    if (!startStr || !endStr) return '';
    try {
      const start = new Date(startStr.replace(' ', 'T')).getTime();
      const end = new Date(endStr.replace(' ', 'T')).getTime();
      const diffSec = Math.max(0, Math.floor((end - start) / 1000));
      const mins = Math.floor(diffSec / 60);
      const secs = diffSec % 60;
      if (mins === 0 && secs === 0) return '';
      return `${mins}m ${secs}s`;
    } catch {
      return '';
    }
  };

  const formatTimestamp = (ts: string): string => {
    if (!ts) return '';
    try {
      const d = new Date(ts.replace(' ', 'T'));
      return d.toLocaleDateString('en-IN', {
        day: 'numeric',
        month: 'short',
        hour: '2-digit',
        minute: '2-digit',
      });
    } catch {
      return ts;
    }
  };

  const getInitials = (name: string) =>
    name
      .split(' ')
      .map((w) => w[0])
      .slice(0, 2)
      .join('')
      .toUpperCase();

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.background} barStyle="dark-content" />

      {loading ? (
        <LeadListSkeleton count={5} />
      ) : logs.length === 0 ? (
        <View style={styles.centerContainer}>
          <View style={styles.emptyIconWrap}>
            <Ionicons name="call-outline" size={48} color={Colors.borderGrey} />
          </View>
          <Text style={styles.emptyTitle}>No Call Logs</Text>
          <Text style={styles.emptySub}>
            Calls made from lead cards will be automatically recorded here.
          </Text>
        </View>
      ) : (
        <>
          <View style={styles.countHeader}>
            <Text style={styles.countText}>{logs.length} call record{logs.length !== 1 ? 's' : ''}</Text>
          </View>
          <FlatList
            data={logs}
            keyExtractor={(item, index) => String(item.id || index)}
            contentContainerStyle={styles.listContent}
            showsVerticalScrollIndicator={false}
            renderItem={({ item }) => {
              const duration = formatDuration(
                item.call_start_datetime,
                item.call_end_datetime
              );
              const name = item.name || 'Unknown Lead';

              return (
                <View style={styles.logCard}>
                  <View style={styles.logAvatar}>
                    <Text style={styles.logAvatarText}>{getInitials(name)}</Text>
                  </View>

                  <View style={styles.logDetails}>
                    <Text style={styles.contactName} numberOfLines={1}>{name}</Text>
                    <Text style={styles.contactNo}>{item.contact_no}</Text>
                    <View style={styles.logMeta}>
                      <Ionicons name="time-outline" size={12} color={Colors.textMuted} />
                      <Text style={styles.timestampText}>
                        {formatTimestamp(item.call_start_datetime || item.created_at || '')}
                      </Text>
                      {duration ? (
                        <>
                          <View style={styles.metaDot} />
                          <Ionicons name="call-outline" size={12} color={Colors.success} />
                          <Text style={styles.durationText}>{duration}</Text>
                        </>
                      ) : null}
                    </View>
                  </View>

                  <TouchableOpacity
                    style={styles.redialBtn}
                    onPress={() => handleRedial(item)}
                    activeOpacity={0.8}
                    hitSlop={{ top: 6, bottom: 6, left: 6, right: 6 }}
                  >
                    <Ionicons name="call" size={16} color={Colors.white} />
                  </TouchableOpacity>
                </View>
              );
            }}
            refreshControl={
              <RefreshControl
                refreshing={refreshing}
                onRefresh={onRefresh}
                colors={[Colors.primary]}
                tintColor={Colors.primary}
              />
            }
          />
        </>
      )}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  countHeader: {
    backgroundColor: Colors.white,
    paddingHorizontal: 16,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#F1F5F9',
  },
  countText: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.grey,
  },
  listContent: {
    padding: 12,
    paddingBottom: 20,
  },
  logCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 14,
    marginBottom: 8,
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
  logAvatar: {
    width: 44,
    height: 44,
    borderRadius: 22,
    backgroundColor: `${Colors.primary}18`,
    borderWidth: 1.5,
    borderColor: `${Colors.primary}25`,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  logAvatarText: {
    fontSize: 14,
    fontWeight: '800',
    color: Colors.primary,
  },
  logDetails: {
    flex: 1,
  },
  contactName: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textDark,
    marginBottom: 2,
  },
  contactNo: {
    fontSize: 13,
    color: Colors.grey,
    fontWeight: '500',
    marginBottom: 4,
  },
  logMeta: {
    flexDirection: 'row',
    alignItems: 'center',
    flexWrap: 'wrap',
  },
  timestampText: {
    fontSize: 11,
    color: Colors.textMuted,
    marginLeft: 4,
  },
  metaDot: {
    width: 3,
    height: 3,
    borderRadius: 1.5,
    backgroundColor: Colors.textMuted,
    marginHorizontal: 5,
  },
  durationText: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.success,
    marginLeft: 3,
  },
  redialBtn: {
    width: 38,
    height: 38,
    borderRadius: 19,
    backgroundColor: '#0284C7',
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#0284C7',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 3,
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
