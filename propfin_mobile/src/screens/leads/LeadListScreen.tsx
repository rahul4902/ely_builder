import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  FlatList,
  StyleSheet,
  TextInput,
  RefreshControl,
  StatusBar,
} from 'react-native';
import { RouteProp, useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { HeaderBar } from '../../components/HeaderBar';
import { LeadCard } from '../../components/LeadCard';
import { LeadListSkeleton } from '../../components/SkeletonLoader';
import { AppLinking } from '../../utils/linking';
import { LeadListItem, RootStackParamList } from '../../types';

type LeadListScreenProps = {
  route: RouteProp<RootStackParamList, 'LeadList'>;
};

const leadListCache = new Map<number, LeadListItem[]>();

export const LeadListScreen = ({ route }: LeadListScreenProps) => {
  const { leadType, title } = route.params;
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const { authKey } = useAuth();

  const [leads, setLeads] = useState<LeadListItem[]>(() => leadListCache.get(leadType) || []);
  const [filteredLeads, setFilteredLeads] = useState<LeadListItem[]>(() => leadListCache.get(leadType) || []);
  const [searchQuery, setSearchQuery] = useState('');
  const [loading, setLoading] = useState<boolean>(() => !leadListCache.has(leadType));
  const [refreshing, setRefreshing] = useState(false);

  const fetchLeads = useCallback(async () => {
    if (!authKey) return;
    try {
      const res = await ApiService.getLeadList(authKey, leadType);
      const list = res?.Lead_List || res?.Leads_list || res?.record || [];
      if (Array.isArray(list)) {
        setLeads(list);
        setFilteredLeads(list);
        leadListCache.set(leadType, list);
      } else {
        setLeads([]);
        setFilteredLeads([]);
        leadListCache.set(leadType, []);
      }
    } catch (e) {
      console.warn('Failed to load lead list', e);
    } finally {
      setLoading(false);
    }
  }, [authKey, leadType]);

  useEffect(() => {
    fetchLeads();
  }, [fetchLeads]);

  const onRefresh = async () => {
    setRefreshing(true);
    leadListCache.delete(leadType);
    await fetchLeads();
    setRefreshing(false);
  };

  const handleSearch = (query: string) => {
    setSearchQuery(query);
    if (!query.trim()) {
      setFilteredLeads(leads);
      return;
    }
    const q = query.toLowerCase();
    const filtered = leads.filter(
      (l) =>
        (l.name && l.name.toLowerCase().includes(q)) ||
        (l.contact_no && l.contact_no.includes(q)) ||
        (l.project && l.project.toLowerCase().includes(q)) ||
        (l.city && l.city.toLowerCase().includes(q))
    );
    setFilteredLeads(filtered);
  };

  const handleCall = async (lead: LeadListItem) => {
    if (!lead.contact_no) return;
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

  const handleChat = (lead: LeadListItem) => {
    if (!lead.contact_no) return;
    AppLinking.openWhatsApp(lead.contact_no);
  };

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      <HeaderBar
        title={title || 'Leads'}
        showBack
        onBackPress={() => navigation.goBack()}
      />

      {/* Search Bar */}
      <View style={styles.searchBarContainer}>
        <View style={styles.searchBox}>
          <Ionicons name="search" size={18} color={Colors.grey} />
          <TextInput
            style={styles.searchInput}
            placeholder="Search by name, phone, city..."
            placeholderTextColor={Colors.textMuted}
            value={searchQuery}
            onChangeText={handleSearch}
            returnKeyType="search"
          />
          {searchQuery.length > 0 && (
            <Ionicons
              name="close-circle"
              size={18}
              color={Colors.grey}
              onPress={() => handleSearch('')}
            />
          )}
        </View>
        {!loading && (
          <Text style={styles.countLabel}>
            {filteredLeads.length} lead{filteredLeads.length !== 1 ? 's' : ''}
          </Text>
        )}
      </View>

      {loading ? (
        <LeadListSkeleton count={5} />
      ) : filteredLeads.length === 0 ? (
        <View style={styles.centerContainer}>
          <View style={styles.emptyIconWrap}>
            <Ionicons name="people-outline" size={56} color={Colors.borderGrey} />
          </View>
          <Text style={styles.emptyTitle}>No Leads Found</Text>
          <Text style={styles.emptySubtitle}>
            {searchQuery
              ? 'No leads match your search. Try a different keyword.'
              : 'No leads available in this category.'}
          </Text>
        </View>
      ) : (
        <FlatList
          data={filteredLeads}
          keyExtractor={(item) => String(item.id)}
          renderItem={({ item }) => (
            <LeadCard
              lead={item}
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
  searchBarContainer: {
    backgroundColor: Colors.white,
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#F1F5F9',
    flexDirection: 'row',
    alignItems: 'center',
  },
  searchBox: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F8FAFC',
    borderRadius: 12,
    paddingHorizontal: 12,
    height: 42,
    borderWidth: 1,
    borderColor: '#E2E8F0',
  },
  searchInput: {
    flex: 1,
    marginLeft: 8,
    fontSize: 14,
    color: Colors.textDark,
    padding: 0,
  },
  countLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.grey,
    marginLeft: 10,
    minWidth: 50,
    textAlign: 'right',
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
