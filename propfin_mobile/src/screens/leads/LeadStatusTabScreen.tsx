import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  StyleSheet,
  RefreshControl,
  StatusBar,
  Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { LeadCard } from '../../components/LeadCard';
import { LeadListSkeleton } from '../../components/SkeletonLoader';
import { AppLinking } from '../../utils/linking';
import { LeadFilterItem, RootStackParamList } from '../../types';

interface LeadStatusTabScreenProps {
  statusFilter: 'new' | 'interested' | 'meeting' | 'visit' | 'booked';
}

const leadStatusCache = new Map<string, LeadFilterItem[]>();

export const LeadStatusTabScreen = ({ statusFilter }: LeadStatusTabScreenProps) => {
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const { authKey } = useAuth();

  const [leads, setLeads] = useState<LeadFilterItem[]>(() => leadStatusCache.get(statusFilter) || []);
  const [loading, setLoading] = useState<boolean>(() => !leadStatusCache.has(statusFilter));
  const [refreshing, setRefreshing] = useState(false);

  const fetchLeads = useCallback(async () => {
    if (!authKey) return;
    try {
      const res = await ApiService.getLeadFilterList(authKey, statusFilter);
      if (res && res.Lead_List) {
        setLeads(res.Lead_List);
        leadStatusCache.set(statusFilter, res.Lead_List);
      } else {
        setLeads([]);
        leadStatusCache.set(statusFilter, []);
      }
    } catch (err) {
      console.warn(`Failed to fetch ${statusFilter} leads`, err);
    } finally {
      setLoading(false);
    }
  }, [authKey, statusFilter]);

  useEffect(() => {
    fetchLeads();
  }, [fetchLeads]);

  const onRefresh = async () => {
    setRefreshing(true);
    leadStatusCache.delete(statusFilter);
    await fetchLeads();
    setRefreshing(false);
  };

  const handleCall = async (lead: LeadFilterItem) => {
    if (!lead.contact_no) {
      Alert.alert('No Number', 'This lead does not have a contact number.');
      return;
    }

    await AppLinking.makePhoneCall(lead.contact_no, lead.id, (time) => {
      const endTime = new Date(time.getTime() + 15000);
      if (authKey && lead.id) {
        ApiService.addCallLog({
          key: authKey,
          lead_id: lead.id,
          call_start_datetime: time.toISOString().replace('T', ' ').substring(0, 19),
          call_end_datetime: endTime.toISOString().replace('T', ' ').substring(0, 19),
        }).catch(() => {});
      }
    });
  };

  const handleChat = (lead: LeadFilterItem) => {
    if (!lead.contact_no) {
      Alert.alert('No Number', 'This lead does not have a contact number.');
      return;
    }
    AppLinking.openWhatsApp(lead.contact_no);
  };

  const getStatusLabel = () => {
    const map: Record<string, string> = {
      new: 'New Leads',
      interested: 'Interested Leads',
      meeting: 'Meeting Done',
      visit: 'Visit Done',
      booked: 'Booking Done',
    };
    return map[statusFilter] || 'Leads';
  };

  return (
    <View style={styles.container}>
      {loading ? (
        <LeadListSkeleton count={5} />
      ) : leads.length === 0 ? (
        <View style={styles.centerContainer}>
          <View style={styles.emptyIconWrap}>
            <Ionicons name="people-outline" size={56} color={Colors.borderGrey} />
          </View>
          <Text style={styles.emptyTitle}>No Leads Found</Text>
          <Text style={styles.emptySubtitle}>
            There are currently no {getStatusLabel().toLowerCase()} in your pipeline.
          </Text>
        </View>
      ) : (
        <FlatList
          data={leads}
          keyExtractor={(item) => String(item.id)}
          renderItem={({ item }) => (
            <LeadCard
              lead={item}
              isVisitTab={statusFilter === 'visit'}
              onDetailPress={() =>
                navigation.navigate('LeadDetail', {
                  leadId: item.id,
                  status: item.status,
                })
              }
              onCalendarPress={() =>
                navigation.navigate('FollowUp', {
                  leadId: item.id,
                })
              }
              onChatPress={() => handleChat(item)}
              onCallPress={() => handleCall(item)}
            />
          )}
          contentContainerStyle={styles.listContent}
          refreshControl={
            <RefreshControl
              refreshing={refreshing}
              onRefresh={onRefresh}
              colors={[Colors.primary]}
              tintColor={Colors.primary}
            />
          }
          showsVerticalScrollIndicator={false}
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
    paddingVertical: 8,
    paddingBottom: 20,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 30,
  },
  emptyIconWrap: {
    width: 100,
    height: 100,
    borderRadius: 50,
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
  emptySubtitle: {
    fontSize: 13,
    color: Colors.grey,
    textAlign: 'center',
    lineHeight: 20,
  },
});
