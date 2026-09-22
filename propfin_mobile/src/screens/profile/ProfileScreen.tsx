import React from 'react';
import {
  View,
  Text,
  StyleSheet,
  TouchableOpacity,
  ScrollView,
  Alert,
  StatusBar,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { AppButton } from '../../components/AppButton';
import { RootStackParamList } from '../../types';

export const ProfileScreen = () => {
  const navigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();
  const { userName, userMobile, logout } = useAuth();

  const handleLogout = () => {
    Alert.alert(
      'Logout',
      'Are you sure you want to logout from ElyLeads?',
      [
        { text: 'Cancel', style: 'cancel' },
        {
          text: 'Yes, Logout',
          style: 'destructive',
          onPress: async () => {
            await logout();
          },
        },
      ],
      { cancelable: true }
    );
  };

  const initials = (userName || 'E')
    .split(' ')
    .map((w) => w[0])
    .slice(0, 2)
    .join('')
    .toUpperCase();

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.background} barStyle="dark-content" />
      <ScrollView contentContainerStyle={styles.scrollContent} showsVerticalScrollIndicator={false}>
        {/* Profile Header */}
        <View style={styles.profileHeader}>
          {/* Avatar with gradient-like ring */}
          <View style={styles.avatarRing}>
            <View style={styles.avatarWrap}>
              <Text style={styles.avatarInitials}>{initials}</Text>
            </View>
          </View>
          <Text style={styles.userNameText}>{userName || 'ElyLeads User'}</Text>
          <Text style={styles.userMobileText}>
            {userMobile ? `+91 ${userMobile}` : 'CRM Agent'}
          </Text>
          <View style={styles.roleBadge}>
            <Ionicons name="shield-checkmark-outline" size={12} color={Colors.primary} />
            <Text style={styles.roleBadgeText}>Sales Executive</Text>
          </View>
        </View>

        {/* Quick Stats Row */}
        <View style={styles.statsRow}>
          <View style={styles.statItem}>
            <Text style={styles.statNum}>—</Text>
            <Text style={styles.statLabel}>My Leads</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <Text style={styles.statNum}>—</Text>
            <Text style={styles.statLabel}>Follow-Ups</Text>
          </View>
          <View style={styles.statDivider} />
          <View style={styles.statItem}>
            <Text style={styles.statNum}>—</Text>
            <Text style={styles.statLabel}>Closed</Text>
          </View>
        </View>

        {/* Settings Options Card */}
        <View style={styles.settingsCard}>
          <Text style={styles.sectionHeader}>Preferences</Text>

          <TouchableOpacity
            style={styles.optionRow}
            onPress={() => navigation.navigate('NotificationSoundSetting')}
            activeOpacity={0.7}
          >
            <View style={styles.optionLeft}>
              <View style={[styles.optionIcon, { backgroundColor: '#E0F2FE' }]}>
                <Ionicons name="volume-medium" size={18} color="#0284C7" />
              </View>
              <Text style={styles.optionTitle}>Notification Sounds</Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color={Colors.grey} />
          </TouchableOpacity>

          <TouchableOpacity
            style={styles.optionRow}
            onPress={() => navigation.navigate('Notifications')}
            activeOpacity={0.7}
          >
            <View style={styles.optionLeft}>
              <View style={[styles.optionIcon, { backgroundColor: '#FEF3C7' }]}>
                <Ionicons name="notifications" size={18} color="#D97706" />
              </View>
              <Text style={styles.optionTitle}>In-App Notifications</Text>
            </View>
            <Ionicons name="chevron-forward" size={16} color={Colors.grey} />
          </TouchableOpacity>
        </View>

        {/* App Info Card */}
        <View style={styles.settingsCard}>
          <Text style={styles.sectionHeader}>About ElyLeads</Text>

          <View style={[styles.optionRow, { borderBottomWidth: 0 }]}>
            <View style={styles.optionLeft}>
              <View style={[styles.optionIcon, { backgroundColor: Colors.primarySubtle }]}>
                <Ionicons name="information-circle" size={18} color={Colors.primary} />
              </View>
              <View>
                <Text style={styles.optionTitle}>Version</Text>
                <Text style={styles.optionSub}>ElyLeads CRM</Text>
              </View>
            </View>
            <Text style={styles.versionText}>1.0.0</Text>
          </View>
        </View>

        {/* Logout */}
        <AppButton
          title="SIGN OUT"
          onPress={handleLogout}
          variant="danger"
          size="large"
          icon={<Ionicons name="log-out-outline" size={20} color={Colors.white} />}
          style={{ marginTop: 4, marginBottom: 10 }}
        />
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
    padding: 16,
    paddingBottom: 30,
  },
  profileHeader: {
    backgroundColor: Colors.white,
    borderRadius: 20,
    padding: 24,
    alignItems: 'center',
    marginBottom: 12,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.07,
    shadowRadius: 6,
  },
  avatarRing: {
    width: 92,
    height: 92,
    borderRadius: 46,
    borderWidth: 3,
    borderColor: `${Colors.primary}30`,
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 14,
    backgroundColor: Colors.primarySubtle,
  },
  avatarWrap: {
    width: 80,
    height: 80,
    borderRadius: 40,
    backgroundColor: Colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  avatarInitials: {
    fontSize: 28,
    fontWeight: '800',
    color: Colors.white,
    letterSpacing: 1,
  },
  userNameText: {
    fontSize: 20,
    fontWeight: '800',
    color: Colors.textDark,
    marginBottom: 4,
  },
  userMobileText: {
    fontSize: 14,
    color: Colors.grey,
    fontWeight: '500',
    marginBottom: 10,
  },
  roleBadge: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: Colors.primarySubtle,
    paddingHorizontal: 12,
    paddingVertical: 5,
    borderRadius: 20,
    borderWidth: 1,
    borderColor: `${Colors.primary}25`,
    gap: 5,
  },
  roleBadgeText: {
    fontSize: 12,
    fontWeight: '700',
    color: Colors.primary,
  },
  statsRow: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    flexDirection: 'row',
    marginBottom: 12,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
  },
  statItem: {
    flex: 1,
    alignItems: 'center',
  },
  statNum: {
    fontSize: 20,
    fontWeight: '800',
    color: Colors.textDark,
  },
  statLabel: {
    fontSize: 11,
    color: Colors.grey,
    fontWeight: '600',
    marginTop: 3,
  },
  statDivider: {
    width: 1,
    backgroundColor: Colors.borderGrey,
    marginVertical: 4,
  },
  settingsCard: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    marginBottom: 12,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.05,
    shadowRadius: 4,
  },
  sectionHeader: {
    fontSize: 11,
    fontWeight: '700',
    color: Colors.grey,
    textTransform: 'uppercase',
    letterSpacing: 1,
    marginBottom: 12,
  },
  optionRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 10,
    borderBottomWidth: 1,
    borderBottomColor: '#F5F5F5',
  },
  optionLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
  },
  optionIcon: {
    width: 36,
    height: 36,
    borderRadius: 10,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  optionTitle: {
    fontSize: 14,
    fontWeight: '600',
    color: Colors.textDark,
  },
  optionSub: {
    fontSize: 11,
    color: Colors.grey,
    marginTop: 1,
  },
  versionText: {
    fontSize: 13,
    color: Colors.grey,
    fontWeight: '600',
  },
});
