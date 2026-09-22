import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Modal,
  TouchableOpacity,
  TextInput,
  ScrollView,
  ActivityIndicator,
  Alert,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { SeniorItem, StatusOptionItem, MeetingTypeItem } from '../../types';
import { showAlert } from '../../utils/alert';
import { SearchableDropdown } from '../../components/SearchableDropdown';
import { AppButton } from '../../components/AppButton';

interface MeetingDoneModalProps {
  visible: boolean;
  leadId: string | number;
  onClose: () => void;
  onSuccess: () => void;
}

export const MeetingDoneModal = ({
  visible,
  leadId,
  onClose,
  onSuccess,
}: MeetingDoneModalProps) => {
  const { authKey } = useAuth();

  const [loading, setLoading] = useState(false);
  const [submitting, setSubmitting] = useState(false);

  const [seniors, setSeniors] = useState<SeniorItem[]>([]);
  const [statuses, setStatuses] = useState<StatusOptionItem[]>([]);
  const [meetingTypes, setMeetingTypes] = useState<MeetingTypeItem[]>([]);

  const [selectedSenior, setSelectedSenior] = useState<string>('');
  const [selectedMeetingType, setSelectedMeetingType] = useState<string>('');
  const [selectedStatus, setSelectedStatus] = useState<string>('');
  const [comment, setComment] = useState<string>('');

  useEffect(() => {
    if (visible && authKey) {
      loadFormData();
    }
  }, [visible, authKey]);

  const loadFormData = async () => {
    if (!authKey) return;
    setLoading(true);
    try {
      const [senRes, statRes, typeRes] = await Promise.all([
        ApiService.getSeniors(authKey).catch(() => ({ record: [] })),
        ApiService.getLeadStatuses(authKey).catch(() => ({ record: [] })),
        ApiService.getMeetingTypes(authKey).catch(() => ({ record: [] })),
      ]);

      if (senRes?.record) {
        setSeniors(senRes.record);
        if (senRes.record.length > 0) setSelectedSenior(senRes.record[0].name);
      }
      if (statRes?.record) {
        setStatuses(statRes.record);
        if (statRes.record.length > 0) setSelectedStatus(statRes.record[0].name);
      }
      if (typeRes?.record) {
        setMeetingTypes(typeRes.record);
        if (typeRes.record.length > 0) setSelectedMeetingType(typeRes.record[0].name);
      }
    } catch (e) {
      console.warn('Failed to load meeting form data', e);
    } finally {
      setLoading(false);
    }
  };

  const handleSubmit = async () => {
    if (!authKey) return;
    if (!comment.trim()) {
      showAlert('Validation Error', 'Please enter comments for the meeting.');
      return;
    }

    setSubmitting(true);
    try {
      await ApiService.addFollowupMeeting({
        key: authKey,
        follow_id: leadId,
        comment: comment.trim(),
        senior_visit: selectedSenior,
        meeting_type: selectedMeetingType,
        follow_up_status: selectedStatus,
      });

      setSubmitting(false);
      showAlert('Success', 'Meeting details logged successfully!', onSuccess);
    } catch (e: any) {
      setSubmitting(false);
      showAlert('Error', e?.message || 'Failed to submit meeting details.');
    }
  };

  return (
    <Modal
      visible={visible}
      transparent
      animationType="slide"
      onRequestClose={onClose}
    >
      <View style={styles.overlay}>
        <View style={styles.modalContent}>
          {/* Header */}
          <View style={styles.modalHeader}>
            <Text style={styles.modalTitle}>Meeting Done</Text>
            <TouchableOpacity onPress={onClose} style={styles.closeBtn}>
              <Ionicons name="close" size={24} color={Colors.grey} />
            </TouchableOpacity>
          </View>

          {loading ? (
            <View style={styles.loadingBox}>
              <ActivityIndicator size="small" color={Colors.primary} />
              <Text style={styles.loadingText}>Loading meeting options...</Text>
            </View>
          ) : (
            <ScrollView contentContainerStyle={styles.bodyScroll}>
              {/* Senior Visit Dropdown */}
              <SearchableDropdown
                label="Senior Visit"
                placeholder="Select Senior..."
                items={seniors.map((s) => s.name)}
                selectedValue={selectedSenior}
                onSelect={setSelectedSenior}
                allowCustomOther={false}
                modalTitle="Select Senior Visit"
              />

              {/* Meeting Type */}
              <Text style={styles.label}>Meeting Type</Text>
              <View style={styles.chipRow}>
                {meetingTypes.map((m) => (
                  <TouchableOpacity
                    key={m.id || m.name}
                    style={[
                      styles.chip,
                      selectedMeetingType === m.name && styles.chipActive,
                    ]}
                    onPress={() => setSelectedMeetingType(m.name)}
                  >
                    <Text
                      style={[
                        styles.chipText,
                        selectedMeetingType === m.name && styles.chipTextActive,
                      ]}
                    >
                      {m.name}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>

              {/* Status */}
              <Text style={styles.label}>Follow-Up Status</Text>
              <View style={styles.chipRow}>
                {statuses.map((st) => (
                  <TouchableOpacity
                    key={st.id || st.name}
                    style={[
                      styles.chip,
                      selectedStatus === st.name && styles.chipActive,
                    ]}
                    onPress={() => setSelectedStatus(st.name)}
                  >
                    <Text
                      style={[
                        styles.chipText,
                        selectedStatus === st.name && styles.chipTextActive,
                      ]}
                    >
                      {st.name}
                    </Text>
                  </TouchableOpacity>
                ))}
              </View>

              {/* Comments */}
              <Text style={styles.label}>Meeting Comments</Text>
              <TextInput
                style={styles.textArea}
                placeholder="Write summary of discussion, next actions..."
                placeholderTextColor="#999"
                multiline
                numberOfLines={3}
                value={comment}
                onChangeText={setComment}
                textAlignVertical="top"
              />

              <AppButton
                title="SUBMIT MEETING"
                onPress={handleSubmit}
                loading={submitting}
                loadingText="Submitting Meeting..."
                disabled={!comment.trim() || submitting}
                size="large"
                style={{ marginTop: 22 }}
              />
            </ScrollView>
          )}
        </View>
      </View>
    </Modal>
  );
};

const styles = StyleSheet.create({
  overlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  modalContent: {
    backgroundColor: Colors.white,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '85%',
    paddingBottom: 24,
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: '#EEEEEE',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.black,
  },
  closeBtn: {
    padding: 4,
  },
  loadingBox: {
    padding: 40,
    alignItems: 'center',
  },
  loadingText: {
    marginTop: 10,
    fontSize: 13,
    color: Colors.grey,
  },
  bodyScroll: {
    padding: 20,
  },
  label: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.black,
    marginTop: 12,
    marginBottom: 8,
  },
  chipRow: {
    flexDirection: 'row',
    flexWrap: 'wrap',
    gap: 8,
  },
  chip: {
    paddingHorizontal: 12,
    paddingVertical: 8,
    borderRadius: 8,
    backgroundColor: '#F3F4F6',
    borderWidth: 1,
    borderColor: '#E5E7EB',
  },
  chipActive: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  chipText: {
    fontSize: 12,
    color: Colors.black,
    fontWeight: '500',
  },
  chipTextActive: {
    color: Colors.white,
    fontWeight: '700',
  },
  textArea: {
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    borderRadius: 10,
    padding: 12,
    fontSize: 14,
    color: Colors.black,
    minHeight: 80,
    backgroundColor: '#FAFAFA',
  },
  submitBtn: {
    backgroundColor: Colors.primary,
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 20,
    elevation: 2,
  },
  submitBtnText: {
    color: Colors.white,
    fontSize: 15,
    fontWeight: '700',
    letterSpacing: 0.5,
  },
});
