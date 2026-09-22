import React, { useState, useEffect, useCallback } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TouchableOpacity,
  ActivityIndicator,
  Alert,
  StatusBar,
} from 'react-native';
import { RouteProp, useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { HeaderBar } from '../../components/HeaderBar';
import { AppLinking } from '../../utils/linking';
import { MeetingDoneModal } from './MeetingDoneModal';
import { EditLeadModal } from './EditLeadModal';
import {
  LeadDetailRecord,
  PreviousFollowupItem,
  RootStackParamList,
} from '../../types';

type LeadDetailScreenProps = {
  route: RouteProp<RootStackParamList, 'LeadDetail'>;
};

const getStatusColors = (status: string = '') => {
  const s = status.toLowerCase();
  if (s === 'open' || s === '1') return { bg: '#ECFDF5', text: '#059669', border: '#A7F3D0' };
  if (s === 'close' || s === 'closed' || s === '2') return { bg: '#FEF2F2', text: '#DC2626', border: '#FECACA' };
  if (s.includes('progress') || s === '3' || s === '4') return { bg: '#FFF7ED', text: '#D97706', border: '#FED7AA' };
  return { bg: Colors.primarySubtle, text: Colors.primary, border: `${Colors.primary}40` };
};

const InfoRow = ({ label, value }: { label: string; value: string }) => (
  <View style={infoRowStyles.row}>
    <Text style={infoRowStyles.label}>{label}</Text>
    <Text style={infoRowStyles.value} numberOfLines={2}>{value || 'N/A'}</Text>
  </View>
);

const infoRowStyles = StyleSheet.create({
  row: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#F5F5F5',
  },
  label: {
    fontSize: 13,
    color: Colors.grey,
    flex: 1,
    fontWeight: '500',
  },
  value: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textDark,
    flex: 1.5,
    textAlign: 'right',
  },
});

export const LeadDetailScreen = ({ route }: LeadDetailScreenProps) => {
  const { leadId, status } = route.params;
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const { authKey } = useAuth();

  const [leadDetail, setLeadDetail] = useState<LeadDetailRecord | null>(null);
  const [followups, setFollowups] = useState<PreviousFollowupItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [meetingModalVisible, setMeetingModalVisible] = useState(false);
  const [editModalVisible, setEditModalVisible] = useState(false);

  const fetchDetail = useCallback(async () => {
    if (!authKey) return;
    try {
      const [detailRes, followRes] = await Promise.all([
        ApiService.getDetailView(authKey, leadId),
        ApiService.getPreviousFollowups(authKey, leadId),
      ]);
      
      const record = detailRes?.record?.[0] || detailRes?.DetailRecord?.[0] || detailRes?.detail || null;
      if (record) {
        setLeadDetail(record);
      }

      const followList = followRes?.followup || followRes?.PreviousFollowup || [];
      if (Array.isArray(followList)) {
        setFollowups(followList);
      }
    } catch (e) {
      console.warn('Failed to fetch lead details', e);
    } finally {
      setLoading(false);
    }
  }, [authKey, leadId]);

  useEffect(() => {
    fetchDetail();
  }, [fetchDetail]);

  const isClosed =
    status?.toLowerCase() === 'close' ||
    status?.toLowerCase() === 'closed' ||
    leadDetail?.status?.toLowerCase() === 'close' ||
    leadDetail?.status?.toLowerCase() === 'closed';

  const handleCall = async () => {
    if (!leadDetail?.contact_no) {
      Alert.alert('No Number', 'No phone number available.');
      return;
    }

    await AppLinking.makePhoneCall(leadDetail.contact_no, String(leadId), (time) => {
      const endTime = new Date(time.getTime() + 15000);
      if (authKey) {
        ApiService.addCallLog({
          key: authKey,
          lead_id: leadId,
          call_start_datetime: time.toISOString().replace('T', ' ').substring(0, 19),
          call_end_datetime: endTime.toISOString().replace('T', ' ').substring(0, 19),
        }).catch(() => {});
      }
    });
  };

  const handleChat = () => {
    if (!leadDetail?.contact_no) {
      Alert.alert('No Number', 'No phone number available.');
      return;
    }
    AppLinking.openWhatsApp(leadDetail.contact_no);
  };

  const statusColors = getStatusColors(leadDetail?.status || status);
  const initials = (leadDetail?.name || 'L')
    .split(' ')
    .map((w: string) => w[0])
    .slice(0, 2)
    .join('')
    .toUpperCase();

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      <HeaderBar
        title="Lead Details"
        showBack
        onBackPress={() => navigation.goBack()}
      />

      {loading ? (
        <View style={styles.centerContainer}>
          <ActivityIndicator size="large" color={Colors.primary} />
          <Text style={styles.loadingText}>Loading lead details...</Text>
        </View>
      ) : !leadDetail ? (
        <View style={styles.centerContainer}>
          <View style={styles.emptyIconWrap}>
            <Ionicons name="person-outline" size={48} color={Colors.borderGrey} />
          </View>
          <Text style={styles.emptyText}>Lead details not found.</Text>
          <TouchableOpacity onPress={() => navigation.goBack()} style={styles.goBackBtn}>
            <Text style={styles.goBackText}>Go Back</Text>
          </TouchableOpacity>
        </View>
      ) : (
        <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
          {/* Main Info Header Card */}
          <View style={styles.mainCard}>
            <View style={styles.headerTopRow}>
              {/* Avatar */}
              <View style={styles.avatar}>
                <Text style={styles.avatarText}>{initials}</Text>
              </View>
              <View style={styles.nameContainer}>
                <Text style={styles.leadName}>{leadDetail.name}</Text>
                {leadDetail.contact_no ? (
                  <Text style={styles.leadPhone}>{leadDetail.contact_no}</Text>
                ) : null}
                <View style={[styles.statusBadge, { backgroundColor: statusColors.bg, borderColor: statusColors.border }]}>
                  <View style={[styles.statusDot, { backgroundColor: statusColors.text }]} />
                  <Text style={[styles.statusBadgeText, { color: statusColors.text }]}>
                    {leadDetail.status || 'Active'}
                  </Text>
                </View>
              </View>
            </View>

            {/* Quick Action Bar */}
            <View style={styles.quickActionsBar}>
              <TouchableOpacity
                style={[styles.quickActionBtn, { backgroundColor: '#0284C7' }]}
                onPress={handleCall}
                activeOpacity={0.8}
              >
                <Ionicons name="call" size={18} color={Colors.white} />
                <Text style={styles.quickActionText}>Call</Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[styles.quickActionBtn, { backgroundColor: '#25D366' }]}
                onPress={handleChat}
                activeOpacity={0.8}
              >
                <Ionicons name="logo-whatsapp" size={18} color={Colors.white} />
                <Text style={styles.quickActionText}>WhatsApp</Text>
              </TouchableOpacity>

              {!isClosed && (
                <TouchableOpacity
                  style={[styles.quickActionBtn, { backgroundColor: '#F59E0B' }]}
                  onPress={() => navigation.navigate('FollowUp', { leadId })}
                  activeOpacity={0.8}
                >
                  <Ionicons name="add-circle" size={18} color={Colors.white} />
                  <Text style={styles.quickActionText}>Follow-Up</Text>
                </TouchableOpacity>
              )}

              <TouchableOpacity
                style={[styles.quickActionBtn, { backgroundColor: '#8B5CF6' }]}
                onPress={() => setMeetingModalVisible(true)}
                activeOpacity={0.8}
              >
                <Ionicons name="people" size={18} color={Colors.white} />
                <Text style={styles.quickActionText}>Meeting</Text>
              </TouchableOpacity>
            </View>
          </View>

          {/* Lead Information Card */}
          <View style={styles.infoCard}>
            <View style={styles.sectionHeaderRow}>
              <View style={styles.sectionTitleWrap}>
                <Ionicons name="information-circle-outline" size={18} color={Colors.primary} />
                <Text style={styles.sectionTitle}>Lead Information</Text>
              </View>
              <TouchableOpacity
                style={styles.editBtn}
                onPress={() => setEditModalVisible(true)}
                activeOpacity={0.7}
              >
                <Ionicons name="create-outline" size={15} color={Colors.primary} />
                <Text style={styles.editBtnText}>Edit</Text>
              </TouchableOpacity>
            </View>

            <InfoRow label="Contact" value={leadDetail.contact_no || 'N/A'} />
            <InfoRow label="Email" value={leadDetail.email || 'N/A'} />
            <InfoRow label="Project" value={leadDetail.project || 'N/A'} />
            <InfoRow label="Requirement" value={leadDetail.requirement || 'N/A'} />
            <InfoRow label="Budget" value={leadDetail.Budget || 'N/A'} />
            <InfoRow label="Source" value={leadDetail.source || 'N/A'} />
            <InfoRow label="Location" value={leadDetail.location || 'N/A'} />
            <InfoRow
              label="City / State"
              value={[leadDetail.city, leadDetail.state].filter(Boolean).join(', ') || 'N/A'}
            />
            {leadDetail.pin ? (
              <InfoRow label="Pincode" value={leadDetail.pin} />
            ) : null}
          </View>

          {/* Follow-up Timeline */}
          <View style={styles.timelineCard}>
            <View style={styles.sectionTitleWrap}>
              <Ionicons name="time-outline" size={18} color={Colors.primary} />
              <Text style={styles.sectionTitle}>Follow-Up History</Text>
            </View>
            {followups.length === 0 ? (
              <View style={styles.emptyTimeline}>
                <Ionicons name="calendar-outline" size={32} color={Colors.borderGrey} />
                <Text style={styles.noTimelineText}>No follow-ups recorded yet.</Text>
              </View>
            ) : (
              followups.map((item, index) => (
                <View key={item.id || index} style={styles.timelineItem}>
                  <View style={styles.timelineBulletWrap}>
                    <View style={styles.timelineBullet} />
                    {index < followups.length - 1 && <View style={styles.timelineLine} />}
                  </View>
                  <View style={styles.timelineContent}>
                    <View style={styles.timelineHeaderRow}>
                      <Text style={styles.timelineDate}>{item.follow_up_date}</Text>
                      <View style={styles.timelineStatusBadge}>
                        <Text style={styles.timelineStatusText}>{item.status}</Text>
                      </View>
                    </View>
                    {item.comment ? (
                      <Text style={styles.timelineComment}>{item.comment}</Text>
                    ) : null}
                    {item.user_name ? (
                      <Text style={styles.timelineUser}>By: {item.user_name}</Text>
                    ) : null}
                  </View>
                </View>
              ))
            )}
          </View>
        </ScrollView>
      )}

      <MeetingDoneModal
        visible={meetingModalVisible}
        leadId={leadId}
        onClose={() => setMeetingModalVisible(false)}
        onSuccess={() => {
          setMeetingModalVisible(false);
          fetchDetail();
        }}
      />

      <EditLeadModal
        visible={editModalVisible}
        leadId={leadId}
        leadDetail={leadDetail}
        onClose={() => setEditModalVisible(false)}
        onSuccess={() => {
          setEditModalVisible(false);
          fetchDetail();
        }}
      />
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
    paddingBottom: 40,
  },
  centerContainer: {
    flex: 1,
    justifyContent: 'center',
    alignItems: 'center',
    padding: 24,
  },
  loadingText: {
    marginTop: 12,
    fontSize: 14,
    color: Colors.grey,
    fontWeight: '500',
  },
  emptyIconWrap: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: Colors.lightGrey,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 14,
  },
  emptyText: {
    fontSize: 16,
    color: Colors.grey,
    fontWeight: '600',
    marginBottom: 12,
  },
  goBackBtn: {
    paddingHorizontal: 20,
    paddingVertical: 10,
    borderRadius: 10,
    backgroundColor: Colors.primarySubtle,
  },
  goBackText: {
    color: Colors.primary,
    fontWeight: '700',
    fontSize: 14,
  },
  mainCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    marginBottom: 14,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.08,
    shadowRadius: 6,
  },
  headerTopRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    marginBottom: 16,
  },
  avatar: {
    width: 54,
    height: 54,
    borderRadius: 27,
    backgroundColor: `${Colors.primary}18`,
    borderWidth: 2,
    borderColor: `${Colors.primary}30`,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 14,
  },
  avatarText: {
    fontSize: 18,
    fontWeight: '800',
    color: Colors.primary,
  },
  nameContainer: {
    flex: 1,
  },
  leadName: {
    fontSize: 18,
    fontWeight: '800',
    color: Colors.textDark,
    marginBottom: 2,
  },
  leadPhone: {
    fontSize: 13,
    color: Colors.grey,
    marginBottom: 6,
    fontWeight: '500',
  },
  statusBadge: {
    flexDirection: 'row',
    alignSelf: 'flex-start',
    paddingHorizontal: 10,
    paddingVertical: 4,
    borderRadius: 20,
    borderWidth: 1,
    alignItems: 'center',
  },
  statusDot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    marginRight: 6,
  },
  statusBadgeText: {
    fontSize: 11,
    fontWeight: '700',
  },
  quickActionsBar: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    paddingTop: 14,
    borderTopWidth: 1,
    borderTopColor: '#F0F0F0',
    gap: 8,
  },
  quickActionBtn: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    paddingHorizontal: 4,
    borderRadius: 10,
    gap: 4,
  },
  quickActionText: {
    color: Colors.white,
    fontSize: 11,
    fontWeight: '700',
  },
  infoCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    marginBottom: 14,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 4,
  },
  sectionHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 12,
  },
  sectionTitleWrap: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 6,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textDark,
    marginLeft: 6,
  },
  editBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 10,
    paddingVertical: 5,
    borderRadius: 8,
    backgroundColor: Colors.primarySubtle,
    gap: 4,
  },
  editBtnText: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.primary,
  },
  timelineCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.06,
    shadowRadius: 4,
    marginBottom: 10,
  },
  emptyTimeline: {
    alignItems: 'center',
    paddingVertical: 20,
  },
  noTimelineText: {
    fontSize: 13,
    color: Colors.grey,
    fontStyle: 'italic',
    marginTop: 8,
  },
  timelineItem: {
    flexDirection: 'row',
    marginBottom: 14,
    marginTop: 12,
  },
  timelineBulletWrap: {
    alignItems: 'center',
    width: 20,
    marginRight: 10,
  },
  timelineBullet: {
    width: 10,
    height: 10,
    borderRadius: 5,
    backgroundColor: Colors.primary,
    marginTop: 5,
  },
  timelineLine: {
    width: 2,
    flex: 1,
    backgroundColor: '#E2E8F0',
    marginTop: 4,
  },
  timelineContent: {
    flex: 1,
    backgroundColor: '#F8FAFC',
    borderRadius: 10,
    padding: 12,
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  timelineHeaderRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    marginBottom: 4,
  },
  timelineDate: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.textDark,
  },
  timelineStatusBadge: {
    backgroundColor: Colors.infoLight,
    paddingHorizontal: 8,
    paddingVertical: 2,
    borderRadius: 6,
  },
  timelineStatusText: {
    fontSize: 10,
    fontWeight: '700',
    color: Colors.info,
  },
  timelineComment: {
    fontSize: 13,
    color: Colors.bodyText,
    lineHeight: 18,
  },
  timelineUser: {
    fontSize: 11,
    color: Colors.grey,
    marginTop: 4,
    fontStyle: 'italic',
  },
});
