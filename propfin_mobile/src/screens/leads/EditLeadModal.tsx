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
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { LeadDetailRecord } from '../../types';
import { showAlert } from '../../utils/alert';
import { AppButton } from '../../components/AppButton';

interface EditLeadModalProps {
  visible: boolean;
  leadId: string | number;
  leadDetail: LeadDetailRecord | null;
  onClose: () => void;
  onSuccess: () => void;
}

export const EditLeadModal = ({
  visible,
  leadId,
  leadDetail,
  onClose,
  onSuccess,
}: EditLeadModalProps) => {
  const { authKey } = useAuth();
  const [submitting, setSubmitting] = useState(false);

  const [name, setName] = useState('');
  const [email, setEmail] = useState('');
  const [contactNo, setContactNo] = useState('');
  const [project, setProject] = useState('');
  const [requirement, setRequirement] = useState('');
  const [budget, setBudget] = useState('');
  const [location, setLocation] = useState('');
  const [city, setCity] = useState('');
  const [state, setState] = useState('');
  const [leadType, setLeadType] = useState<'Hot' | 'Cold'>('Hot');

  useEffect(() => {
    if (visible && leadDetail) {
      setName(leadDetail.name || '');
      setEmail(leadDetail.email || '');
      setContactNo(leadDetail.contact_no || '');
      setProject(leadDetail.project || '');
      setRequirement(leadDetail.requirement || '');
      setBudget(leadDetail.Budget || '');
      setLocation(leadDetail.location || '');
      setCity(leadDetail.city || '');
      setState(leadDetail.state || '');
      const rawType = (leadDetail.lead_type || '').toLowerCase();
      setLeadType(rawType === 'cold' ? 'Cold' : 'Hot');
    }
  }, [visible, leadDetail]);

  const handleSubmit = async () => {
    if (!authKey) return;
    if (!name.trim()) {
      showAlert('Validation Error', 'Please enter the lead name.');
      return;
    }
    if (!email.trim()) {
      showAlert('Validation Error', 'Please enter the email address.');
      return;
    }

    setSubmitting(true);
    try {
      await ApiService.updateLead({
        key: authKey,
        lead_id: leadId,
        name: name.trim(),
        email: email.trim(),
        state: state.trim(),
        city: city.trim(),
        location: location.trim(),
        project: project.trim(),
        requirement: requirement.trim(),
        Budget: budget.trim(),
        lead_type: leadType,
      });

      showAlert('Success', 'Lead updated successfully!');
      onSuccess();
    } catch (e: any) {
      showAlert('Error', e?.message || 'Failed to update lead. Please try again.');
    } finally {
      setSubmitting(false);
    }
  };

  return (
    <Modal
      visible={visible}
      animationType="slide"
      transparent={true}
      onRequestClose={onClose}
    >
      <KeyboardAvoidingView
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
        style={styles.modalOverlay}
      >
        <View style={styles.modalContainer}>
          {/* Header */}
          <View style={styles.modalHeader}>
            <View style={styles.modalTitleRow}>
              <Ionicons name="create-outline" size={22} color={Colors.primary} />
              <Text style={styles.modalTitle}>Edit Lead Details</Text>
            </View>
            <TouchableOpacity onPress={onClose} style={styles.closeBtn}>
              <Ionicons name="close" size={24} color={Colors.grey} />
            </TouchableOpacity>
          </View>

          <ScrollView contentContainerStyle={styles.scrollContent} keyboardShouldPersistTaps="handled">
            {/* Lead Name */}
            <Text style={styles.fieldLabel}>Name *</Text>
            <TextInput
              style={styles.input}
              value={name}
              onChangeText={setName}
              placeholder="Full Name"
              placeholderTextColor="#94A3B8"
            />

            {/* Mobile (Contact) */}
            <Text style={styles.fieldLabel}>Contact Number</Text>
            <TextInput
              style={[styles.input, styles.readOnlyInput]}
              value={contactNo}
              editable={false}
              placeholder="Contact Number"
              placeholderTextColor="#94A3B8"
            />

            {/* Email */}
            <Text style={styles.fieldLabel}>Email *</Text>
            <TextInput
              style={styles.input}
              value={email}
              onChangeText={setEmail}
              placeholder="Email Address"
              placeholderTextColor="#94A3B8"
              keyboardType="email-address"
              autoCapitalize="none"
            />

            {/* Lead Type (Hot / Cold) */}
            <Text style={styles.fieldLabel}>Lead Type</Text>
            <View style={styles.typeSelectorRow}>
              <TouchableOpacity
                style={[
                  styles.typePill,
                  leadType === 'Hot' && styles.typePillActiveHot,
                ]}
                onPress={() => setLeadType('Hot')}
                activeOpacity={0.8}
              >
                <Ionicons
                  name="flame"
                  size={16}
                  color={leadType === 'Hot' ? Colors.white : '#EF4444'}
                />
                <Text
                  style={[
                    styles.typePillText,
                    leadType === 'Hot' && styles.typePillTextActive,
                  ]}
                >
                  Hot Lead
                </Text>
              </TouchableOpacity>

              <TouchableOpacity
                style={[
                  styles.typePill,
                  leadType === 'Cold' && styles.typePillActiveCold,
                ]}
                onPress={() => setLeadType('Cold')}
                activeOpacity={0.8}
              >
                <Ionicons
                  name="snow"
                  size={16}
                  color={leadType === 'Cold' ? Colors.white : '#3B82F6'}
                />
                <Text
                  style={[
                    styles.typePillText,
                    leadType === 'Cold' && styles.typePillTextActive,
                  ]}
                >
                  Cold Lead
                </Text>
              </TouchableOpacity>
            </View>

            {/* Project */}
            <Text style={styles.fieldLabel}>Project</Text>
            <TextInput
              style={styles.input}
              value={project}
              onChangeText={setProject}
              placeholder="Project Name"
              placeholderTextColor="#94A3B8"
            />

            {/* Requirement */}
            <Text style={styles.fieldLabel}>Requirement</Text>
            <TextInput
              style={styles.input}
              value={requirement}
              onChangeText={setRequirement}
              placeholder="e.g. 2 BHK, 3 BHK, Villa"
              placeholderTextColor="#94A3B8"
            />

            {/* Budget */}
            <Text style={styles.fieldLabel}>Budget</Text>
            <TextInput
              style={styles.input}
              value={budget}
              onChangeText={setBudget}
              placeholder="e.g. 50L - 1Cr"
              placeholderTextColor="#94A3B8"
            />

            {/* Location */}
            <Text style={styles.fieldLabel}>Location</Text>
            <TextInput
              style={styles.input}
              value={location}
              onChangeText={setLocation}
              placeholder="Area / Sector / Landmark"
              placeholderTextColor="#94A3B8"
            />

            {/* City & State */}
            <View style={styles.twoColRow}>
              <View style={styles.halfCol}>
                <Text style={styles.fieldLabel}>City</Text>
                <TextInput
                  style={styles.input}
                  value={city}
                  onChangeText={setCity}
                  placeholder="City"
                  placeholderTextColor="#94A3B8"
                />
              </View>
              <View style={styles.halfCol}>
                <Text style={styles.fieldLabel}>State</Text>
                <TextInput
                  style={styles.input}
                  value={state}
                  onChangeText={setState}
                  placeholder="State"
                  placeholderTextColor="#94A3B8"
                />
              </View>
            </View>
          </ScrollView>

          {/* Footer Actions */}
          <View style={styles.modalFooter}>
            <TouchableOpacity
              style={styles.cancelBtn}
              onPress={onClose}
              disabled={submitting}
            >
              <Text style={styles.cancelBtnText}>Cancel</Text>
            </TouchableOpacity>

            <AppButton
              title="Save Changes"
              onPress={handleSubmit}
              loading={submitting}
              loadingText="Saving..."
              disabled={!name.trim() || !email.trim() || submitting}
              size="medium"
              style={{ flex: 2 }}
              icon={<Ionicons name="save-outline" size={18} color={Colors.white} />}
            />
          </View>
        </View>
      </KeyboardAvoidingView>
    </Modal>
  );
};

const styles = StyleSheet.create({
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(15, 23, 42, 0.65)',
    justifyContent: 'flex-end',
  },
  modalContainer: {
    backgroundColor: Colors.white,
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '90%',
    display: 'flex',
  },
  modalHeader: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
    paddingHorizontal: 20,
    paddingVertical: 16,
    borderBottomWidth: 1,
    borderBottomColor: Colors.borderGrey,
  },
  modalTitleRow: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  modalTitle: {
    fontSize: 18,
    fontWeight: '700',
    color: Colors.textDark,
    marginLeft: 8,
  },
  closeBtn: {
    padding: 4,
  },
  scrollContent: {
    paddingHorizontal: 20,
    paddingVertical: 14,
  },
  fieldLabel: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.bodyText,
    marginBottom: 6,
    marginTop: 10,
  },
  input: {
    backgroundColor: '#F8FAFC',
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    borderRadius: 8,
    paddingHorizontal: 14,
    paddingVertical: 10,
    fontSize: 14,
    color: Colors.textDark,
  },
  readOnlyInput: {
    backgroundColor: '#F1F5F9',
    color: Colors.grey,
  },
  typeSelectorRow: {
    flexDirection: 'row',
    gap: 12,
    marginTop: 4,
  },
  typePill: {
    flex: 1,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 10,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    backgroundColor: '#F8FAFC',
    gap: 6,
  },
  typePillActiveHot: {
    backgroundColor: '#EF4444',
    borderColor: '#EF4444',
  },
  typePillActiveCold: {
    backgroundColor: '#3B82F6',
    borderColor: '#3B82F6',
  },
  typePillText: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.bodyText,
  },
  typePillTextActive: {
    color: Colors.white,
  },
  twoColRow: {
    flexDirection: 'row',
    gap: 12,
  },
  halfCol: {
    flex: 1,
  },
  modalFooter: {
    flexDirection: 'row',
    paddingHorizontal: 20,
    paddingVertical: 14,
    borderTopWidth: 1,
    borderTopColor: Colors.borderGrey,
    gap: 12,
  },
  cancelBtn: {
    flex: 1,
    paddingVertical: 12,
    borderRadius: 8,
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    alignItems: 'center',
    justifyContent: 'center',
  },
  cancelBtnText: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.grey,
  },
  submitBtn: {
    flex: 2,
    backgroundColor: Colors.primary,
    paddingVertical: 12,
    borderRadius: 8,
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 6,
    elevation: 2,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.3,
    shadowRadius: 3,
  },
  submitBtnText: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.white,
  },
});
