import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  Switch,
  TouchableOpacity,
  Alert,
} from 'react-native';
import { useNavigation } from '@react-navigation/native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { Storage } from '../../utils/storage';
import { HeaderBar } from '../../components/HeaderBar';
import { AppButton } from '../../components/AppButton';
import { showAlert } from '../../utils/alert';

export const NotificationSoundScreen = () => {
  const navigation = useNavigation();

  const [soundEnabled, setSoundEnabled] = useState(true);
  const [vibrateEnabled, setVibrateEnabled] = useState(true);
  const [pushEnabled, setPushEnabled] = useState(true);

  useEffect(() => {
    loadSettings();
  }, []);

  const loadSettings = async () => {
    const sound = await Storage.getNotificationSound();
    setSoundEnabled(sound);
  };

  const handleToggleSound = async (val: boolean) => {
    setSoundEnabled(val);
    await Storage.setNotificationSound(val);
  };

  const handleSave = () => {
    showAlert('Settings Saved', 'Your notification preferences have been saved.', () => {
      if (navigation.canGoBack()) {
        navigation.goBack();
      }
    });
  };

  return (
    <View style={styles.container}>
      <HeaderBar
        title="Notification Settings"
        showBack
        onBackPress={() => navigation.goBack()}
      />

      <View style={styles.content}>
        <View style={styles.card}>
          <View style={styles.settingRow}>
            <View style={styles.settingInfo}>
              <Ionicons name="volume-high-outline" size={22} color={Colors.primary} />
              <View style={styles.textWrap}>
                <Text style={styles.settingTitle}>Notification Sound</Text>
                <Text style={styles.settingSub}>
                  Play audio alerts when new leads or updates arrive
                </Text>
              </View>
            </View>
            <Switch
              value={soundEnabled}
              onValueChange={handleToggleSound}
              trackColor={{ false: '#D1D5DB', true: Colors.primary }}
              thumbColor={Colors.white}
            />
          </View>

          <View style={styles.divider} />

          <View style={styles.settingRow}>
            <View style={styles.settingInfo}>
              <Ionicons name="phone-portrait-outline" size={22} color={Colors.primary} />
              <View style={styles.textWrap}>
                <Text style={styles.settingTitle}>Vibration</Text>
                <Text style={styles.settingSub}>
                  Vibrate on receiving high priority follow-up alerts
                </Text>
              </View>
            </View>
            <Switch
              value={vibrateEnabled}
              onValueChange={setVibrateEnabled}
              trackColor={{ false: '#D1D5DB', true: Colors.primary }}
              thumbColor={Colors.white}
            />
          </View>

          <View style={styles.divider} />

          <View style={styles.settingRow}>
            <View style={styles.settingInfo}>
              <Ionicons name="notifications-outline" size={22} color={Colors.primary} />
              <View style={styles.textWrap}>
                <Text style={styles.settingTitle}>Push Notifications</Text>
                <Text style={styles.settingSub}>
                  Allow in-app and device push reminders
                </Text>
              </View>
            </View>
            <Switch
              value={pushEnabled}
              onValueChange={setPushEnabled}
              trackColor={{ false: '#D1D5DB', true: Colors.primary }}
              thumbColor={Colors.white}
            />
          </View>
        </View>

        <View style={{ marginTop: 24 }}>
          <AppButton
            title="Save Preferences"
            onPress={handleSave}
            icon="save-outline"
            variant="primary"
          />
        </View>
      </View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
  content: {
    padding: 16,
  },
  card: {
    backgroundColor: Colors.white,
    borderRadius: 14,
    padding: 16,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
  },
  settingRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 12,
  },
  settingInfo: {
    flexDirection: 'row',
    alignItems: 'center',
    flex: 1,
    paddingRight: 10,
  },
  textWrap: {
    marginLeft: 12,
    flex: 1,
  },
  settingTitle: {
    fontSize: 15,
    fontWeight: '700',
    color: Colors.black,
  },
  settingSub: {
    fontSize: 12,
    color: Colors.grey,
    marginTop: 2,
  },
  divider: {
    height: 1,
    backgroundColor: '#F3F4F6',
  },
  saveBtn: {
    backgroundColor: Colors.primary,
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 24,
    elevation: 2,
  },
  saveBtnText: {
    color: Colors.white,
    fontSize: 15,
    fontWeight: '700',
    letterSpacing: 0.8,
  },
});
