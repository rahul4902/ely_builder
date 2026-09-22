import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/colors';
import { LeadFilterItem, LeadListItem } from '../types';

interface LeadCardProps {
  lead: LeadFilterItem | LeadListItem;
  isVisitTab?: boolean;
  onDetailPress: () => void;
  onCalendarPress: () => void;
  onChatPress: () => void;
  onCallPress: () => void;
}

const getLeadTypeBadge = (lead: any) => {
  const leadType = (lead.lead_type || lead.leadType || '').toLowerCase();
  if (leadType === 'hot') return { label: '🔥 Hot', bg: '#FEF2F2', text: '#DC2626', border: '#FECACA' };
  if (leadType === 'cold') return { label: '❄️ Cold', bg: '#EFF6FF', text: '#2563EB', border: '#BFDBFE' };
  return null;
};

const getStatusColor = (status: string = '') => {
  const s = status.toLowerCase();
  if (s === 'open' || s === '1') return { bg: '#ECFDF5', text: '#059669' };
  if (s === 'close' || s === 'closed' || s === '2') return { bg: '#FEF2F2', text: '#DC2626' };
  if (s === 'in process' || s === '3' || s === '4') return { bg: '#FFF7ED', text: '#D97706' };
  return { bg: '#F3F4F6', text: '#6B7280' };
};

export const LeadCard = ({
  lead,
  isVisitTab = false,
  onDetailPress,
  onCalendarPress,
  onChatPress,
  onCallPress,
}: LeadCardProps) => {
  const projectRequirement = lead.project
    ? lead.requirement
      ? `${lead.project} (${lead.requirement})`
      : lead.project
    : lead.requirement || '';

  const typeBadge = getLeadTypeBadge(lead);
  const statusColor = getStatusColor((lead as any).status);

  const initials = (lead.name || 'L')
    .split(' ')
    .map((w: string) => w[0])
    .slice(0, 2)
    .join('')
    .toUpperCase();

  return (
    <View style={styles.card}>
      <View style={styles.contentRow}>
        {/* Avatar */}
        <View style={styles.avatarWrap}>
          <View style={styles.avatar}>
            <Text style={styles.avatarText}>{initials}</Text>
          </View>
          {typeBadge && (
            <View style={[styles.hotColdDot, { backgroundColor: typeBadge.bg, borderColor: typeBadge.border }]}>
              <Text style={[styles.hotColdText, { color: typeBadge.text }]}>{typeBadge.label.split(' ')[0]}</Text>
            </View>
          )}
        </View>

        {/* Info */}
        <View style={styles.infoCol}>
          <View style={styles.nameRow}>
            <Text style={styles.nameText} numberOfLines={1}>
              {lead.name || 'Unnamed Lead'}
            </Text>
            {(lead as any).status ? (
              <View style={[styles.statusBadge, { backgroundColor: statusColor.bg }]}>
                <Text style={[styles.statusBadgeText, { color: statusColor.text }]}>
                  {(lead as any).status}
                </Text>
              </View>
            ) : null}
          </View>

          {lead.contact_no ? (
            <View style={styles.detailRow}>
              <Ionicons name="call-outline" size={13} color={Colors.grey} />
              <Text style={styles.numberText} numberOfLines={1}>
                {lead.contact_no}
              </Text>
            </View>
          ) : null}

          {(lead.city || lead.location) ? (
            <View style={styles.detailRow}>
              <Ionicons name="location-outline" size={13} color={Colors.grey} />
              <Text style={styles.cityText} numberOfLines={1}>
                {lead.city || lead.location}
              </Text>
            </View>
          ) : null}

          {projectRequirement ? (
            <View style={styles.detailRow}>
              <Ionicons name="business-outline" size={13} color={Colors.grey} />
              <Text style={styles.projectText} numberOfLines={1}>
                {projectRequirement}
              </Text>
            </View>
          ) : null}

          <View style={styles.badgeRow}>
            {(lead.nextfollowup || (lead as any).next_follow_up || (lead as any).follow_up_date) ? (
              <View style={styles.tagBadge}>
                <Ionicons name="time-outline" size={11} color="#D97706" />
                <Text style={styles.followUpText} numberOfLines={1}>
                  {lead.nextfollowup || (lead as any).next_follow_up || (lead as any).follow_up_date}
                </Text>
              </View>
            ) : null}

            {(lead.meetingdate || (lead as any).meeting_date) ? (
              <View style={[styles.tagBadge, { backgroundColor: '#EFF6FF', borderColor: '#BFDBFE' }]}>
                <Ionicons name="calendar-outline" size={11} color="#2563EB" />
                <Text style={[styles.followUpText, { color: '#2563EB' }]} numberOfLines={1}>
                  {isVisitTab ? 'Visit' : 'Mtg'}: {lead.meetingdate || (lead as any).meeting_date}
                </Text>
              </View>
            ) : null}
          </View>
        </View>

        {/* Action Buttons */}
        <View style={styles.actionsCol}>
          <TouchableOpacity
            style={[styles.circleBtn, { backgroundColor: Colors.primary }]}
            onPress={onDetailPress}
            activeOpacity={0.8}
            hitSlop={{ top: 4, bottom: 4, left: 4, right: 4 }}
          >
            <Ionicons name="arrow-forward" size={15} color={Colors.white} />
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.circleBtn, { backgroundColor: '#F59E0B' }]}
            onPress={onCalendarPress}
            activeOpacity={0.8}
            hitSlop={{ top: 4, bottom: 4, left: 4, right: 4 }}
          >
            <Ionicons name="calendar" size={15} color={Colors.white} />
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.circleBtn, { backgroundColor: '#25D366' }]}
            onPress={onChatPress}
            activeOpacity={0.8}
            hitSlop={{ top: 4, bottom: 4, left: 4, right: 4 }}
          >
            <Ionicons name="logo-whatsapp" size={15} color={Colors.white} />
          </TouchableOpacity>

          <TouchableOpacity
            style={[styles.circleBtn, { backgroundColor: '#0284C7' }]}
            onPress={onCallPress}
            activeOpacity={0.8}
            hitSlop={{ top: 4, bottom: 4, left: 4, right: 4 }}
          >
            <Ionicons name="call" size={15} color={Colors.white} />
          </TouchableOpacity>
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  card: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    marginHorizontal: 12,
    marginVertical: 5,
    padding: 12,
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.06,
    shadowRadius: 6,
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  contentRow: {
    flexDirection: 'row',
    alignItems: 'flex-start',
  },
  avatarWrap: {
    marginRight: 10,
    alignItems: 'center',
    position: 'relative',
  },
  avatar: {
    width: 42,
    height: 42,
    borderRadius: 21,
    backgroundColor: `${Colors.primary}18`,
    borderWidth: 1.5,
    borderColor: `${Colors.primary}30`,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarText: {
    fontSize: 14,
    fontWeight: '800',
    color: Colors.primary,
  },
  hotColdDot: {
    marginTop: 4,
    paddingHorizontal: 5,
    paddingVertical: 2,
    borderRadius: 6,
    borderWidth: 1,
    alignItems: 'center',
  },
  hotColdText: {
    fontSize: 9,
    fontWeight: '700',
  },
  infoCol: {
    flex: 1,
    paddingRight: 8,
  },
  nameRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    marginBottom: 3,
  },
  nameText: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textDark,
    flex: 1,
    marginRight: 6,
  },
  statusBadge: {
    paddingHorizontal: 6,
    paddingVertical: 2,
    borderRadius: 6,
  },
  statusBadgeText: {
    fontSize: 10,
    fontWeight: '700',
  },
  detailRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 3,
  },
  cityText: {
    fontSize: 12,
    color: Colors.grey,
    marginLeft: 5,
    flex: 1,
  },
  projectText: {
    fontSize: 12,
    color: Colors.bodyText,
    marginLeft: 5,
    fontWeight: '500',
    flex: 1,
  },
  numberText: {
    fontSize: 12,
    color: Colors.textDark,
    marginLeft: 5,
    fontWeight: '600',
  },
  badgeRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    marginTop: 6,
    gap: 6,
  },
  tagBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FEF3C7',
    paddingHorizontal: 7,
    paddingVertical: 3,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: '#FDE68A',
  },
  followUpText: {
    fontSize: 10,
    color: '#D97706',
    fontWeight: '700',
    marginLeft: 3,
  },
  actionsCol: {
    flexDirection: 'column',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 7,
  },
  circleBtn: {
    width: 34,
    height: 34,
    borderRadius: 17,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.15,
    shadowRadius: 2,
  },
});
