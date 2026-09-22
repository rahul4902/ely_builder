import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  ScrollView,
  StatusBar,
  KeyboardAvoidingView,
  Platform,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { HeaderBar } from '../../components/HeaderBar';
import { AppButton } from '../../components/AppButton';
import { SearchableDropdown } from '../../components/SearchableDropdown';
import { showAlert } from '../../utils/alert';
import { MasterItem, RootStackParamList } from '../../types';

type NewLeadScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, 'NewLead'>;
};

const DEFAULT_SOURCES = ['Personal', 'Reference', '99 Acre', 'MagicBricks', 'Facebook', 'Other'];
const DEFAULT_REQUIREMENTS = ['1 BHK', '2 BHK', '3 BHK', '4 BHK', 'Plot', 'Commercial', 'Other'];
const DEFAULT_BUDGETS = ['1.49Lac - 3Lac Rs.', '3Lac - 7.49Lac Rs.', '7.5Lac - 13Lac Rs.', '15Lac - 30Lac Rs.', '30Lac - 50Lac Rs.', '50Lac+ Rs.', 'Other'];
const DEFAULT_PROJECTS = ['Parsvnath', 'Gaur City', 'DLF', 'Omaxe', 'Emaar MGF', 'Raheja', 'Other'];
const LEAD_TYPES = ['Hot', 'Cold'];

interface FieldErrors {
  name?: string;
  contactNo?: string;
}

export const NewLeadScreen = ({ navigation }: NewLeadScreenProps) => {
  const { authKey } = useAuth();

  const [loadingMaster, setLoadingMaster] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [touched, setTouched] = useState<Record<string, boolean>>({});

  // Master lists
  const [sources, setSources] = useState<string[]>(DEFAULT_SOURCES);
  const [projects, setProjects] = useState<string[]>(DEFAULT_PROJECTS);
  const [requirements, setRequirements] = useState<string[]>(DEFAULT_REQUIREMENTS);
  const [budgets, setBudgets] = useState<string[]>(DEFAULT_BUDGETS);

  // Form State
  const [name, setName] = useState('');
  const [contactNo, setContactNo] = useState('');
  const [email, setEmail] = useState('');
  const [state, setState] = useState('Delhi NCR');
  const [city, setCity] = useState('');
  const [pincode, setPincode] = useState('');
  const [location, setLocation] = useState('');

  const [selectedSource, setSelectedSource] = useState(DEFAULT_SOURCES[0]);
  const [otherSource, setOtherSource] = useState('');

  const [selectedProject, setSelectedProject] = useState(DEFAULT_PROJECTS[0]);
  const [otherProject, setOtherProject] = useState('');

  const [selectedRequirement, setSelectedRequirement] = useState(DEFAULT_REQUIREMENTS[0]);
  const [otherRequirement, setOtherRequirement] = useState('');

  const [selectedBudget, setSelectedBudget] = useState(DEFAULT_BUDGETS[0]);
  const [otherBudget, setOtherBudget] = useState('');

  const [leadType, setLeadType] = useState('Hot');

  useEffect(() => {
    loadMasterData();
  }, []);

  const loadMasterData = async () => {
    if (!authKey) return;
    try {
      const res = await ApiService.getLeadMaster(authKey);
      if (res) {
        if (res.source && res.source.length > 0) {
          const s = res.source
            .map((item: MasterItem) => (item.source_name || item.name || '').trim())
            .filter((val: string) => val.length > 0);
          if (s.length > 0) {
            if (!s.includes('Other')) s.push('Other');
            setSources(s);
            setSelectedSource(s[0]);
          }
        }
        if (res.project && res.project.length > 0) {
          const p = res.project
            .map((item: MasterItem) => (item.project_name || item.name || '').trim())
            .filter((val: string) => val.length > 0);
          if (p.length > 0) {
            if (!p.includes('Other')) p.push('Other');
            setProjects(p);
            setSelectedProject(p[0]);
          }
        }
        if (res.requirement && res.requirement.length > 0) {
          const r = res.requirement
            .map((item: MasterItem) => (item.requirement_name || item.name || '').trim())
            .filter((val: string) => val.length > 0);
          if (r.length > 0) {
            if (!r.includes('Other')) r.push('Other');
            setRequirements(r);
            setSelectedRequirement(r[0]);
          }
        }
        if (res.budget && res.budget.length > 0) {
          const b = res.budget
            .map((item: MasterItem) => (item.budget_range || item.name || '').trim())
            .filter((val: string) => val.length > 0);
          if (b.length > 0) {
            if (!b.includes('Other')) b.push('Other');
            setBudgets(b);
            setSelectedBudget(b[0]);
          }
        }
      }
    } catch (e) {
      console.warn('Master data load failed, using defaults', e);
    } finally {
      setLoadingMaster(false);
    }
  };

  const errors: FieldErrors = {};
  if (touched.name && !name.trim()) errors.name = 'Client name is required';
  const cleanPhone = contactNo.replace(/\D/g, '');
  if (touched.contactNo && cleanPhone.length !== 10) errors.contactNo = 'Enter a valid 10-digit number';

  const isFormValid = name.trim() && cleanPhone.length === 10;

  const handleSubmit = async () => {
    setTouched({ name: true, contactNo: true });
    if (!authKey) return;
    if (!name.trim()) return;
    if (cleanPhone.length !== 10) return;

    const finalSource = selectedSource === 'Other' ? otherSource.trim() : selectedSource;
    const finalProject = selectedProject === 'Other' ? otherProject.trim() : selectedProject;
    const finalRequirement = selectedRequirement === 'Other' ? otherRequirement.trim() : selectedRequirement;
    const finalBudget = selectedBudget === 'Other' ? otherBudget.trim() : selectedBudget;

    setSubmitting(true);
    try {
      await ApiService.createNewLead({
        key: authKey,
        name: name.trim(),
        contact_no: cleanPhone,
        email: email.trim(),
        country: 'India',
        state: state.trim(),
        city: city.trim(),
        Pincode: pincode.trim(),
        location: location.trim(),
        source: finalSource,
        project: finalProject,
        requrement: finalRequirement,
        Budget: finalBudget,
        lead_type: leadType,
      });

      setSubmitting(false);
      showAlert('Success', 'Lead created successfully!', () => {
        if (navigation.canGoBack()) {
          navigation.goBack();
        } else {
          navigation.navigate('MainApp');
        }
      });
    } catch (e: any) {
      setSubmitting(false);
      showAlert('Submission Error', e?.message || 'Failed to create new lead.');
    }
  };

  const renderInput = (
    label: string,
    value: string,
    onChange: (v: string) => void,
    options?: {
      placeholder?: string;
      keyboardType?: any;
      maxLength?: number;
      required?: boolean;
      error?: string;
      fieldKey?: string;
    }
  ) => {
    const hasError = !!options?.error;
    return (
      <View style={styles.fieldWrap}>
        <Text style={styles.label}>
          {label}
          {options?.required && <Text style={styles.required}> *</Text>}
        </Text>
        <TextInput
          style={[styles.input, hasError && styles.inputError]}
          placeholder={options?.placeholder || label}
          placeholderTextColor={Colors.textMuted}
          value={value}
          onChangeText={onChange}
          keyboardType={options?.keyboardType || 'default'}
          maxLength={options?.maxLength}
          onBlur={() => options?.fieldKey && setTouched(prev => ({ ...prev, [options.fieldKey!]: true }))}
        />
        {hasError && (
          <View style={styles.errorRow}>
            <Ionicons name="alert-circle-outline" size={12} color={Colors.danger} />
            <Text style={styles.errorText}>{options?.error}</Text>
          </View>
        )}
      </View>
    );
  };

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      <HeaderBar
        title="Create New Lead"
        showBack
        onBackPress={() => navigation.goBack()}
      />

      <KeyboardAvoidingView
        style={{ flex: 1 }}
        behavior={Platform.OS === 'ios' ? 'padding' : undefined}
      >
        <ScrollView
          contentContainerStyle={styles.scrollContent}
          keyboardShouldPersistTaps="handled"
          showsVerticalScrollIndicator={false}
        >
          {/* Lead Type */}
          <View style={styles.card}>
            <View style={styles.sectionHeader}>
              <Ionicons name="flame-outline" size={18} color={Colors.primary} />
              <Text style={styles.sectionTitle}>Lead Priority</Text>
            </View>
            <View style={styles.rowChips}>
              {LEAD_TYPES.map((t) => (
                <TouchableOpacity
                  key={t}
                  style={[
                    styles.chip,
                    leadType === t && (t === 'Hot' ? styles.chipHot : styles.chipCold),
                  ]}
                  onPress={() => setLeadType(t)}
                  activeOpacity={0.8}
                >
                  <Ionicons
                    name={t === 'Hot' ? 'flame' : 'snow'}
                    size={15}
                    color={leadType === t ? Colors.white : (t === 'Hot' ? '#EF4444' : '#3B82F6')}
                  />
                  <Text
                    style={[
                      styles.chipText,
                      leadType === t && styles.chipTextActive,
                    ]}
                  >
                    {t} Lead
                  </Text>
                </TouchableOpacity>
              ))}
            </View>
          </View>

          {/* Basic Details */}
          <View style={styles.card}>
            <View style={styles.sectionHeader}>
              <Ionicons name="person-outline" size={18} color={Colors.primary} />
              <Text style={styles.sectionTitle}>Client Details</Text>
            </View>

            {renderInput('Client Name', name, setName, {
              placeholder: 'e.g. Rajesh Sharma',
              required: true,
              error: errors.name,
              fieldKey: 'name',
            })}

            {renderInput('Contact Number', contactNo, setContactNo, {
              placeholder: '10-digit mobile number',
              keyboardType: 'phone-pad',
              maxLength: 10,
              required: true,
              error: errors.contactNo,
              fieldKey: 'contactNo',
            })}

            {renderInput('Email Address', email, setEmail, {
              placeholder: 'client@example.com',
              keyboardType: 'email-address',
            })}
          </View>

          {/* Property Preferences */}
          <View style={styles.card}>
            <View style={styles.sectionHeader}>
              <Ionicons name="business-outline" size={18} color={Colors.primary} />
              <Text style={styles.sectionTitle}>Property Preferences</Text>
            </View>

            {loadingMaster ? (
              <View style={styles.masterLoadingWrap}>
                <View style={styles.masterLoadingBar} />
                <View style={[styles.masterLoadingBar, { width: '70%' }]} />
                <View style={[styles.masterLoadingBar, { width: '85%' }]} />
              </View>
            ) : (
              <>
                <SearchableDropdown
                  label="Project"
                  placeholder="Select Project..."
                  items={projects}
                  selectedValue={selectedProject}
                  onSelect={setSelectedProject}
                  allowCustomOther
                  customOtherValue={otherProject}
                  onChangeCustomOther={setOtherProject}
                  customOtherPlaceholder="Enter custom project name"
                  modalTitle="Select Project"
                />

                <SearchableDropdown
                  label="Requirement"
                  placeholder="Select Requirement..."
                  items={requirements}
                  selectedValue={selectedRequirement}
                  onSelect={setSelectedRequirement}
                  allowCustomOther
                  customOtherValue={otherRequirement}
                  onChangeCustomOther={setOtherRequirement}
                  customOtherPlaceholder="Enter custom requirement"
                  modalTitle="Select Requirement"
                />

                <SearchableDropdown
                  label="Budget"
                  placeholder="Select Budget Range..."
                  items={budgets}
                  selectedValue={selectedBudget}
                  onSelect={setSelectedBudget}
                  allowCustomOther
                  customOtherValue={otherBudget}
                  onChangeCustomOther={setOtherBudget}
                  customOtherPlaceholder="Enter custom budget"
                  modalTitle="Select Budget Range"
                />

                <SearchableDropdown
                  label="Lead Source"
                  placeholder="Select Lead Source..."
                  items={sources}
                  selectedValue={selectedSource}
                  onSelect={setSelectedSource}
                  allowCustomOther
                  customOtherValue={otherSource}
                  onChangeCustomOther={setOtherSource}
                  customOtherPlaceholder="Enter custom lead source"
                  modalTitle="Select Lead Source"
                />
              </>
            )}
          </View>

          {/* Location Details */}
          <View style={styles.card}>
            <View style={styles.sectionHeader}>
              <Ionicons name="location-outline" size={18} color={Colors.primary} />
              <Text style={styles.sectionTitle}>Location</Text>
            </View>

            {renderInput('Location / Area', location, setLocation, { placeholder: 'Sector / Area / Landmark' })}

            <View style={styles.rowFields}>
              <View style={styles.halfField}>
                {renderInput('City', city, setCity, { placeholder: 'e.g. Noida' })}
              </View>
              <View style={styles.halfField}>
                {renderInput('State', state, setState, { placeholder: 'State' })}
              </View>
            </View>

            {renderInput('Pincode', pincode, setPincode, {
              placeholder: 'e.g. 201301',
              keyboardType: 'numeric',
              maxLength: 6,
            })}
          </View>

          {/* Submit */}
          <AppButton
            title="CREATE LEAD"
            onPress={handleSubmit}
            loading={submitting}
            loadingText="Creating Lead..."
            disabled={!isFormValid || submitting || loadingMaster}
            size="large"
            style={{ marginBottom: 10 }}
          />
        </ScrollView>
      </KeyboardAvoidingView>
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
  },
  sectionTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.textDark,
    marginLeft: 8,
  },
  fieldWrap: {
    marginBottom: 12,
  },
  label: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.bodyText,
    marginBottom: 6,
  },
  required: {
    color: Colors.danger,
  },
  input: {
    borderWidth: 1.5,
    borderColor: Colors.borderGrey,
    borderRadius: 10,
    paddingHorizontal: 14,
    paddingVertical: 11,
    fontSize: 14,
    color: Colors.textDark,
    backgroundColor: '#FAFAFA',
  },
  inputError: {
    borderColor: Colors.danger,
    backgroundColor: '#FFF5F5',
  },
  errorRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 4,
  },
  errorText: {
    fontSize: 11,
    color: Colors.danger,
    marginLeft: 4,
    fontWeight: '500',
  },
  rowChips: {
    flexDirection: 'row',
    gap: 10,
  },
  chip: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
    paddingHorizontal: 14,
    paddingVertical: 10,
    borderRadius: 10,
    backgroundColor: '#F8FAFC',
    borderWidth: 1.5,
    borderColor: '#E2E8F0',
    justifyContent: 'center',
    gap: 6,
  },
  chipHot: {
    backgroundColor: '#EF4444',
    borderColor: '#EF4444',
  },
  chipCold: {
    backgroundColor: '#3B82F6',
    borderColor: '#3B82F6',
  },
  chipText: {
    fontSize: 13,
    color: Colors.textDark,
    fontWeight: '600',
  },
  chipTextActive: {
    color: Colors.white,
    fontWeight: '700',
  },
  rowFields: {
    flexDirection: 'row',
    gap: 12,
  },
  halfField: {
    flex: 1,
  },
  masterLoadingWrap: {
    padding: 8,
  },
  masterLoadingBar: {
    height: 14,
    borderRadius: 7,
    backgroundColor: '#E2E8F0',
    marginBottom: 10,
    width: '100%',
  },
});
