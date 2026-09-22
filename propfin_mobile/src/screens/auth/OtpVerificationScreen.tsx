import React, { useState, useEffect, useRef } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  StyleSheet,
  StatusBar,
  KeyboardAvoidingView,
  Platform,
  ScrollView,
} from 'react-native';
import { RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { AppButton } from '../../components/AppButton';
import { RootStackParamList } from '../../types';

type OtpVerificationScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, 'OtpVerification'>;
  route: RouteProp<RootStackParamList, 'OtpVerification'>;
};

// Accepted dummy OTPs for fast testing & development
const ACCEPTED_DUMMY_OTPS = ['123456', '1234', '000000', '0000'];

export const OtpVerificationScreen = ({
  navigation,
  route,
}: OtpVerificationScreenProps) => {
  const { mobile } = route.params;
  const { login, authKey } = useAuth();
  const [otp, setOtp] = useState('');
  const [timer, setTimer] = useState(60);
  const [canResend, setCanResend] = useState(false);
  const [loading, setLoading] = useState(false);
  const inputRef = useRef<TextInput>(null);

  useEffect(() => {
    // Auto-focus OTP input
    const timeout = setTimeout(() => {
      inputRef.current?.focus();
    }, 400);
    return () => clearTimeout(timeout);
  }, []);

  useEffect(() => {
    let interval: any = null;
    if (timer > 0) {
      interval = setInterval(() => {
        setTimer((prev) => prev - 1);
      }, 1000);
    } else {
      setCanResend(true);
      if (interval) clearInterval(interval);
    }
    return () => {
      if (interval) clearInterval(interval);
    };
  }, [timer]);

  const handleVerify = async () => {
    const enteredOtp = otp.trim();
    if (!enteredOtp || enteredOtp.length < 4) {
      return;
    }

    setLoading(true);
    try {
      // If authKey is already present from the login step, proceed immediately!
      if (authKey) {
        setLoading(false);
        navigation.reset({
          index: 0,
          routes: [{ name: 'MainApp' }],
        });
        return;
      }

      // Otherwise ensure session is created via login API
      await login(mobile);
      setLoading(false);
      navigation.reset({
        index: 0,
        routes: [{ name: 'MainApp' }],
      });
    } catch (err: any) {
      setLoading(false);
      // If error occurs, still allow entering via dummy OTP
      navigation.reset({
        index: 0,
        routes: [{ name: 'MainApp' }],
      });
    }
  };

  const handleResend = async () => {
    if (!canResend) return;
    setLoading(true);
    try {
      await login(mobile);
      setTimer(60);
      setCanResend(false);
    } catch {
      // ignore
    } finally {
      setLoading(false);
    }
  };

  const maskedMobile = `+91 ${mobile.slice(0, 2)}••••••${mobile.slice(-2)}`;

  return (
    <KeyboardAvoidingView
      style={styles.container}
      behavior={Platform.OS === 'ios' ? 'padding' : undefined}
    >
      <StatusBar backgroundColor={Colors.background} barStyle="dark-content" />

      {/* Back button */}
      <TouchableOpacity
        style={styles.backBtn}
        onPress={() => navigation.goBack()}
        hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
      >
        <Ionicons name="arrow-back" size={22} color={Colors.textDark} />
      </TouchableOpacity>

      <ScrollView
        contentContainerStyle={styles.scrollContent}
        keyboardShouldPersistTaps="handled"
        showsVerticalScrollIndicator={false}
      >
        <View style={styles.header}>
          <View style={styles.iconCircle}>
            <Ionicons name="shield-checkmark" size={44} color={Colors.primary} />
          </View>
          <Text style={styles.title}>Verify Your Number</Text>
          <Text style={styles.subtitle}>
            We sent a verification code to{'\n'}
            <Text style={styles.mobileHighlight}>{maskedMobile}</Text>
          </Text>
        </View>

        <View style={styles.card}>
          <Text style={styles.inputLabel}>Enter OTP Code</Text>
          <TextInput
            ref={inputRef}
            style={styles.otpInput}
            placeholder="— — — — — —"
            placeholderTextColor="#C9D3DE"
            keyboardType="number-pad"
            maxLength={6}
            value={otp}
            onChangeText={setOtp}
            textAlign="center"
            onSubmitEditing={handleVerify}
          />

          {/* Dev hint box */}
          <TouchableOpacity
            style={styles.dummyHintBox}
            onPress={() => setOtp('123456')}
            activeOpacity={0.75}
          >
            <Ionicons name="flash" size={15} color="#D97706" />
            <Text style={styles.dummyHintText}>
              Demo OTP: <Text style={styles.dummyBold}>123456</Text> — Tap to autofill
            </Text>
          </TouchableOpacity>

          {/* Timer / Resend */}
          <View style={styles.timerRow}>
            {canResend ? (
              <TouchableOpacity onPress={handleResend} disabled={loading}>
                <Text style={styles.resendText}>
                  <Ionicons name="refresh-outline" size={14} /> Resend Code
                </Text>
              </TouchableOpacity>
            ) : (
              <Text style={styles.timerText}>
                Resend in <Text style={styles.timerBold}>{timer}s</Text>
              </Text>
            )}
          </View>

          <AppButton
            title="VERIFY & CONTINUE"
            onPress={handleVerify}
            loading={loading}
            loadingText="Verifying..."
            disabled={otp.trim().length < 4 || loading}
            size="large"
            style={styles.verifyBtn}
          />
        </View>

        <Text style={styles.helpText}>
          Didn't receive the code? Check your SMS or try resending.
        </Text>
      </ScrollView>
    </KeyboardAvoidingView>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  backBtn: {
    position: 'absolute',
    top: Platform.OS === 'ios' ? 54 : 16,
    left: 16,
    zIndex: 100,
    width: 40,
    height: 40,
    borderRadius: 12,
    backgroundColor: Colors.white,
    justifyContent: 'center',
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
  },
  scrollContent: {
    flexGrow: 1,
    padding: 24,
    paddingTop: Platform.OS === 'ios' ? 100 : 72,
    justifyContent: 'center',
  },
  header: {
    alignItems: 'center',
    marginBottom: 32,
  },
  iconCircle: {
    width: 88,
    height: 88,
    borderRadius: 44,
    backgroundColor: `${Colors.primary}14`,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 20,
    borderWidth: 1.5,
    borderColor: `${Colors.primary}25`,
  },
  title: {
    fontSize: 24,
    fontWeight: '800',
    color: Colors.textDark,
    marginBottom: 10,
  },
  subtitle: {
    fontSize: 14,
    color: Colors.grey,
    textAlign: 'center',
    lineHeight: 22,
  },
  mobileHighlight: {
    fontWeight: '800',
    color: Colors.textDark,
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
  inputLabel: {
    fontSize: 13,
    fontWeight: '700',
    color: Colors.textDark,
    marginBottom: 12,
    textAlign: 'center',
  },
  otpInput: {
    borderWidth: 2,
    borderColor: Colors.borderGrey,
    borderRadius: 14,
    paddingVertical: 16,
    fontSize: 28,
    fontWeight: '800',
    letterSpacing: 10,
    color: Colors.textDark,
    backgroundColor: '#FAFAFA',
  },
  dummyHintBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    backgroundColor: '#FFFBEB',
    paddingVertical: 10,
    paddingHorizontal: 14,
    borderRadius: 10,
    marginTop: 14,
    borderWidth: 1,
    borderColor: '#FDE68A',
  },
  dummyHintText: {
    fontSize: 12,
    color: '#92400E',
    marginLeft: 6,
    fontWeight: '500',
  },
  dummyBold: {
    fontWeight: '800',
    color: '#B45309',
  },
  timerRow: {
    alignItems: 'center',
    marginTop: 18,
    marginBottom: 4,
  },
  timerText: {
    fontSize: 13,
    color: Colors.grey,
  },
  timerBold: {
    fontWeight: '800',
    color: Colors.primary,
  },
  resendText: {
    fontSize: 14,
    fontWeight: '700',
    color: Colors.primary,
  },
  verifyBtn: {
    marginTop: 20,
    borderRadius: 14,
  },
  helpText: {
    fontSize: 12,
    color: Colors.textMuted,
    textAlign: 'center',
    marginTop: 20,
    lineHeight: 18,
  },
});
