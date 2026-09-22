import React, { useState } from 'react';
import {
  View,
  Text,
  StyleSheet,
  ScrollView,
  TextInput,
  TouchableOpacity,
  StatusBar,
  Platform,
} from 'react-native';
import { RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { HeaderBar } from '../../components/HeaderBar';
import { AppButton } from '../../components/AppButton';
import { showAlert } from '../../utils/alert';
import { RootStackParamList } from '../../types';

type FollowUpScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, 'FollowUp'>;
  route: RouteProp<RootStackParamList, 'FollowUp'>;
};

const STATUS_LIST = [
  { label: 'In Progress', value: 'In Progress Lead', icon: 'sync-outline', color: '#F59E0B' },
  { label: 'Close Lead', value: 'Close Lead', icon: 'checkmark-circle-outline', color: Colors.success },
  { label: 'No Answer', value: 'Number Not Valid', icon: 'call-outline', color: Colors.grey },
  { label: 'Broker', value: 'Broker', icon: 'business-outline', color: '#6366F1' },
  { label: 'Not Interested', value: 'Not Interested', icon: 'close-circle-outline', color: Colors.danger },
];

const getTomorrow = () => {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  return d.toISOString().split('T')[0];
};

export const FollowUpScreen = ({ navigation, route }: FollowUpScreenProps) => {
  const { leadId } = route.params;
  const { authKey } = useAuth();

  const [status, setStatus] = useState('In Progress Lead');
  const [date, setDate] = useState(getTomorrow());
  const [time, setTime] = useState('11:00');
  const [comment, setComment] = useState('');
  const [loading, setLoading] = useState(false);
  const [commentTouched, setCommentTouched] = useState(false);

  const isCloseLead = status === 'Close Lead';
  const commentError = commentTouched && !comment.trim();

  const handleSubmit = async () => {
    setCommentTouched(true);
    if (!authKey) return;
    if (!comment.trim()) {
      return;
    }

    // Basic date validation
    if (!isCloseLead && (!date.match(/^\d{4}-\d{2}-\d{2}$/) || !time.match(/^\d{2}:\d{2}$/))) {
      showAlert('Validation Error', 'Please enter a valid date (YYYY-MM-DD) and time (HH:MM).');
      return;
    }

    setLoading(true);
    const combinedDateTime = `${date} ${time}:00`;
    const backendStatus = isCloseLead ? '2' : status;

    try {
      await ApiService.addNewFollowup({
        key: authKey,
        lead_id: leadId,
        follow_up_date: isCloseLead ? '' : combinedDateTime,
        comment: comment.trim(),
        status: backendStatus,
      });

      setLoading(false);
      showAlert('Success', 'Follow-up added successfully!', () => {
        if (isCloseLead) {
          navigation.navigate('MainApp');
        } else if (navigation.canGoBack()) {
          navigation.goBack();
        } else {
          navigation.navigate('MainApp');
        }
      });
    } catch (e: any) {
      setLoading(false);
      showAlert('Error', e?.message || 'Failed to submit follow-up.');
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      <HeaderBar
        title="Add Follow-Up"
        showBack
        onBackPress={() => navigation.goBack()}
      />

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        {/* Status Selection */}
        <View style={styles.card}>
          <View style={styles.sectionHeader}>
            <Ionicons name="flag-outline" size={18} color={Colors.primary} />
            <Text style={styles.sectionTitle}>Lead Status</Text>
          </View>
          <View style={styles.statusGrid}>
            {STATUS_LIST.map((item) => {
              const isActive = status === item.value;
              return (
                <TouchableOpacity
                  key={item.value}
                  style={[
                    styles.statusChip,
                    isActive && { backgroundColor: item.color, borderColor: item.color },
                  ]}
                  onPress={() => setStatus(item.value)}
                  activeOpacity={0.8}
                >
                  <Ionicons
                    name={item.icon as any}
                    size={15}
                    color={isActive ? Colors.white : item.color}
                  />
                  <Text
                    style={[
                      styles.statusChipText,
                      isActive && styles.statusChipTextActive,
                    ]}
                  >
                    {item.label}
                  </Text>
                </TouchableOpacity>
              );
            })}
          </View>

          {isCloseLead && (
            <View style={styles.closeBanner}>
              <Ionicons name="information-circle" size={18} color={Colors.success} />
              <Text style={styles.closeBannerText}>
                This lead will be marked as closed. No follow-up date required.
              </Text>
            </View>
          )}
        </View>

        {/* Date & Time (Hidden when status is Close Lead) */}
        {!isCloseLead && (
          <View style={styles.card}>
            <View style={styles.sectionHeader}>
              <Ionicons name="calendar-outline" size={18} color={Colors.primary} />
              <Text style={styles.sectionTitle}>Next Follow-Up Schedule</Text>
            </View>

            <View style={styles.dateTimeRow}>
              <View style={styles.dateTimeField}>
                <Text style={styles.fieldLabel}>Date</Text>
                <View style={styles.inputWithIcon}>
                  <Ionicons name="calendar-outline" size={16} color={Colors.grey} />
                  <TextInput
                    style={styles.fieldInput}
                    value={date}
                    onChangeText={setDate}
                    placeholder="YYYY-MM-DD"
                    placeholderTextColor={Colors.textMuted}
                  />
                </View>
              </View>

              <View style={styles.dateTimeField}>
                <Text style={styles.fieldLabel}>Time</Text>
                <View style={styles.inputWithIcon}>
                  <Ionicons name="time-outline" size={16} color={Colors.grey} />
                  <TextInput
                    style={styles.fieldInput}
                    value={time}
                    onChangeText={setTime}
                    placeholder="HH:MM"
                    placeholderTextColor={Colors.textMuted}
                    keyboardType="numbers-and-punctuation"
                  />
                </View>
              </View>
            </View>
          </View>
        )}

        {/* Comments */}
        <View style={styles.card}>
          <View style={styles.sectionHeader}>
            <Ionicons name="chatbubble-outline" size={18} color={Colors.primary} />
            <Text style={styles.sectionTitle}>Discussion Notes *</Text>
          </View>
          <TextInput
            style={[styles.textArea, commentError && styles.textAreaError]}
            placeholder="Enter discussion points, outcomes, next steps..."
            placeholderTextColor={Colors.textMuted}
            multiline
            numberOfLines={4}
            value={comment}
            onChangeText={(v) => { setComment(v); setCommentTouched(true); }}
            textAlignVertical="top"
            onBlur={() => setCommentTouched(true)}
          />
          {commentError && (
            <View style={styles.errorRow}>
              <Ionicons name="alert-circle-outline" size={13} color={Colors.danger} />
              <Text style={styles.errorText}>Discussion notes are required</Text>
            </View>
          )}

          <AppButton
            title={isCloseLead ? 'CLOSE LEAD' : 'SAVE FOLLOW-UP'}
            onPress={handleSubmit}
            loading={loading}
            loadingText="Saving..."
            disabled={!comment.trim() || loading}
            variant={isCloseLead ? 'danger' : 'primary'}
            size="large"
            style={{ marginTop: 18 }}
          />
        </View>
      </ScrollView>
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
    paddingBottom: 30,
  },
  card: {
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
  sectionHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 14,
    gap: 6,
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textDark,
    marginLeft: 6,
  },
  statusGrid: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  statusChip: {
    flexDirection: 'row',
    alignItems: 'center',
    paddingHorizontal: 12,
    paddingVertical: 9,
    borderRadius: 10,
    backgroundColor: '#F8FAFC',
    borderWidth: 1.5,
    borderColor: '#E2E8F0',
    gap: 6,
  },
  statusChipText: {
    fontSize: 12,
    color: Colors.textDark,
    fontWeight: '600',
  },
  statusChipTextActive: {
    color: Colors.white,
    fontWeight: '700',
  },
  closeBanner: {
    flexDirection: 'row',
    alignItems: 'flex-start',
    backgroundColor: '#F0FDF4',
    borderRadius: 10,
    padding: 12,
    marginTop: 14,
    borderWidth: 1,
    borderColor: '#D1FAE5',
    gap: 8,
  },
  closeBannerText: {
    fontSize: 12,
    color: Colors.success,
    fontWeight: '600',
    flex: 1,
    lineHeight: 17,
  },
  dateTimeRow: {
    flexDirection: 'row',
    gap: 12,
  },
  dateTimeField: {
    flex: 1,
  },
  fieldLabel: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.textDark,
    marginBottom: 6,
  },
  inputWithIcon: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1.5,
    borderColor: Colors.borderGrey,
    borderRadius: 10,
    paddingHorizontal: 10,
    backgroundColor: '#FAFAFA',
  },
  fieldInput: {
    flex: 1,
    paddingVertical: 11,
    paddingLeft: 8,
    fontSize: 14,
    color: Colors.textDark,
  },
  textArea: {
    borderWidth: 1.5,
    borderColor: Colors.borderGrey,
    borderRadius: 12,
    padding: 14,
    fontSize: 14,
    color: Colors.textDark,
    minHeight: 110,
    backgroundColor: '#FAFAFA',
    lineHeight: 20,
  },
  textAreaError: {
    borderColor: Colors.danger,
    backgroundColor: '#FFF5F5',
  },
  errorRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 6,
  },
  errorText: {
    fontSize: 12,
    color: Colors.danger,
    marginLeft: 4,
    fontWeight: '500',
  },
});
