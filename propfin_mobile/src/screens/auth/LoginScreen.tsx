import React, { useState } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  Image,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
  StatusBar,
} from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { AppButton } from '../../components/AppButton';
import { RootStackParamList } from '../../types';

type LoginScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, 'Login'>;
};

export const LoginScreen = ({ navigation }: LoginScreenProps) => {
  const [mobile, setMobile] = useState('');
  const [loading, setLoading] = useState(false);
  const [touched, setTouched] = useState(false);
  const [focused, setFocused] = useState(false);
  const { login } = useAuth();

  const cleaned = mobile.replace(/\D/g, '').trim();
  const isValidMobile = cleaned.length === 10;
  const showError = touched && !isValidMobile && mobile.length > 0;

  const handleLogin = async () => {
    setTouched(true);
    if (!isValidMobile) {
      return;
    }

    setLoading(true);
    try {
      const res = await login(cleaned);
      setLoading(false);

      if (res.success) {
        navigation.navigate('OtpVerification', { mobile: cleaned });
      } else {
        navigation.navigate('OtpVerification', { mobile: cleaned });
      }
    } catch (err: any) {
      setLoading(false);
      navigation.navigate('OtpVerification', { mobile: cleaned });
    }
  };

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      
      {/* Top Wave Header */}
      <View style={styles.topHeader}>
        <View style={styles.bgCircle} />
        <Image
          source={require('../../../assets/logo.png')}
          style={styles.logo}
          resizeMode="contain"
        />
        <Text style={styles.appName}>ElyLeads</Text>
        <Text style={styles.headerSubtitle}>Real Estate CRM</Text>
      </View>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.card}>
          <Text style={styles.welcomeTitle}>Welcome Back 👋</Text>
          <Text style={styles.welcomeSubtitle}>Sign in with your registered mobile number to continue</Text>

          <View style={styles.fieldWrap}>
            <Text style={styles.inputLabel}>Mobile Number</Text>
            <View style={[
              styles.inputRow,
              focused && styles.inputRowFocused,
              showError && styles.inputRowError,
            ]}>
              <View style={styles.countryCode}>
                <Ionicons name="call-outline" size={16} color={focused ? Colors.primary : Colors.grey} />
                <Text style={[styles.countryCodeText, focused && styles.countryCodeTextFocused]}>+91</Text>
              </View>
              <TextInput
                style={styles.input}
                placeholder="10-digit mobile number"
                placeholderTextColor="#B0BEC5"
                keyboardType="phone-pad"
                maxLength={10}
                value={mobile}
                onChangeText={(v) => { setMobile(v); setTouched(false); }}
                onFocus={() => setFocused(true)}
                onBlur={() => { setFocused(false); setTouched(true); }}
                autoFocus={false}
                returnKeyType="done"
                onSubmitEditing={handleLogin}
              />
              {isValidMobile && (
                <Ionicons name="checkmark-circle" size={20} color={Colors.success} style={styles.trailingIcon} />
              )}
            </View>
            {showError && (
              <View style={styles.errorRow}>
                <Ionicons name="alert-circle-outline" size={13} color={Colors.danger} />
                <Text style={styles.errorText}>Please enter a valid 10-digit mobile number</Text>
              </View>
            )}
          </View>

          <AppButton
            title="CONTINUE"
            onPress={handleLogin}
            loading={loading}
            loadingText="Signing In..."
            disabled={!isValidMobile || loading}
            size="large"
            style={styles.submitBtn}
          />

          <Text style={styles.legalNote}>
            By continuing, you agree to the Terms of Service and Privacy Policy.
          </Text>
        </View>

        <View style={styles.supportRow}>
          <Ionicons name="headset-outline" size={16} color={Colors.grey} />
          <Text style={styles.supportText}> Contact support if you face login issues</Text>
        </View>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  topHeader: {
    backgroundColor: Colors.primary,
    paddingTop: 36,
    paddingBottom: 40,
    alignItems: 'center',
    borderBottomLeftRadius: 32,
    borderBottomRightRadius: 32,
    overflow: 'hidden',
    elevation: 6,
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.35,
    shadowRadius: 8,
  },
  bgCircle: {
    position: 'absolute',
    width: 250,
    height: 250,
    borderRadius: 125,
    backgroundColor: 'rgba(255, 255, 255, 0.08)',
    top: -80,
    right: -60,
  },
  logo: {
    width: 72,
    height: 72,
    marginBottom: 10,
    borderRadius: 18,
    backgroundColor: 'rgba(255,255,255,0.18)',
  },
  appName: {
    fontSize: 24,
    fontWeight: '800',
    color: Colors.white,
    letterSpacing: 1,
  },
  headerSubtitle: {
    fontSize: 12,
    color: 'rgba(255,255,255,0.75)',
    fontWeight: '500',
    marginTop: 4,
    letterSpacing: 0.5,
  },
  scrollContent: {
    flexGrow: 1,
    padding: 20,
    paddingTop: 24,
  },
  card: {
    backgroundColor: Colors.white,
    borderRadius: 20,
    padding: 24,
    elevation: 4,
    shadowColor: '#1E293B',
    shadowOffset: { width: 0, height: 4 },
    shadowOpacity: 0.1,
    shadowRadius: 12,
  },
  welcomeTitle: {
    fontSize: 22,
    fontWeight: '800',
    color: Colors.textDark,
    marginBottom: 6,
  },
  welcomeSubtitle: {
    fontSize: 13,
    color: Colors.grey,
    lineHeight: 18,
    marginBottom: 24,
  },
  fieldWrap: {
    marginBottom: 8,
  },
  inputLabel: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textDark,
    marginBottom: 8,
  },
  inputRow: {
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1.5,
    borderColor: Colors.borderGrey,
    borderRadius: 12,
    backgroundColor: '#FAFAFA',
    overflow: 'hidden',
  },
  inputRowFocused: {
    borderColor: Colors.primary,
    backgroundColor: '#FFFDF9',
    shadowColor: Colors.primary,
    shadowOffset: { width: 0, height: 0 },
    shadowOpacity: 0.15,
    shadowRadius: 4,
    elevation: 0,
  },
  inputRowError: {
    borderColor: Colors.danger,
    backgroundColor: '#FFF5F5',
  },
  countryCode: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F0F2F5',
    paddingHorizontal: 12,
    paddingVertical: 13,
    borderRightWidth: 1,
    borderRightColor: Colors.borderGrey,
  },
  countryCodeText: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.grey,
    marginLeft: 5,
  },
  countryCodeTextFocused: {
    color: Colors.primary,
  },
  input: {
    flex: 1,
    paddingHorizontal: 14,
    paddingVertical: 13,
    fontSize: 16,
    color: Colors.textDark,
    letterSpacing: 0.5,
  },
  trailingIcon: {
    marginRight: 12,
  },
  errorRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginTop: 6,
    marginLeft: 2,
  },
  errorText: {
    fontSize: 12,
    color: Colors.danger,
    marginLeft: 4,
    fontWeight: '500',
  },
  submitBtn: {
    marginTop: 24,
    borderRadius: 14,
  },
  legalNote: {
    fontSize: 11,
    color: Colors.textMuted,
    textAlign: 'center',
    marginTop: 14,
    lineHeight: 16,
  },
  supportRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    marginTop: 20,
    marginBottom: 20,
  },
  supportText: {
    fontSize: 12,
    color: Colors.grey,
    fontWeight: '500',
  },
});
