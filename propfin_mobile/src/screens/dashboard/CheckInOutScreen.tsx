import React, { useState, useEffect } from 'react';
import {
  View,
  Text,
  StyleSheet,
  TextInput,
  TouchableOpacity,
  ScrollView,
  Alert,
  ActivityIndicator,
  StatusBar,
} from 'react-native';
import { RouteProp } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import * as Location from 'expo-location';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { ApiService } from '../../api/services';
import { HeaderBar } from '../../components/HeaderBar';
import { AppButton } from '../../components/AppButton';
import { showAlert } from '../../utils/alert';
import { RootStackParamList } from '../../types';

type CheckInOutScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, 'CheckInOut'>;
  route: RouteProp<RootStackParamList, 'CheckInOut'>;
};

export const CheckInOutScreen = ({
  navigation,
  route,
}: CheckInOutScreenProps) => {
  const { type } = route.params;
  const isCheckIn = type === 'checkIn';
  const { authKey } = useAuth();

  const [loading, setLoading] = useState(false);
  const [fetchingLocation, setFetchingLocation] = useState(true);
  const [coords, setCoords] = useState<{ lat: number; lng: number } | null>(null);
  const [address, setAddress] = useState<string>('');
  const [remarks, setRemarks] = useState<string>('');

  useEffect(() => {
    getCurrentLocation();
  }, []);

  const getCurrentLocation = async () => {
    setFetchingLocation(true);
    try {
      const { status } = await Location.requestForegroundPermissionsAsync();
      if (status !== 'granted') {
        Alert.alert(
          'Permission Denied',
          'Location permission is required for attendance check-in/out.'
        );
        setFetchingLocation(false);
        return;
      }

      const location = await Location.getCurrentPositionAsync({
        accuracy: Location.Accuracy.High,
      });

      const { latitude, longitude } = location.coords;
      setCoords({ lat: latitude, lng: longitude });

      // Reverse geocode to street address
      const geocode = await Location.reverseGeocodeAsync({
        latitude,
        longitude,
      });

      if (geocode && geocode.length > 0) {
        const item = geocode[0];
        const formatted = [
          item.name,
          item.street,
          item.district,
          item.city,
          item.region,
          item.postalCode,
        ]
          .filter(Boolean)
          .join(', ');
        setAddress(formatted || 'Location detected');
      } else {
        setAddress(`${latitude.toFixed(6)}, ${longitude.toFixed(6)}`);
      }
    } catch (e: any) {
      Alert.alert('Location Error', 'Unable to retrieve current GPS location.');
      setAddress('Unable to detect address');
    } finally {
      setFetchingLocation(false);
    }
  };

  const handleSubmit = async () => {
    if (!authKey) return;
    if (!coords) {
      Alert.alert('Wait for GPS', 'Please wait for your GPS coordinates to load.');
      return;
    }

    setLoading(true);
    const now = new Date();
    const formattedDate = now.toISOString().replace('T', ' ').substring(0, 19);

    try {
      if (isCheckIn) {
        await ApiService.employeeCheckIn({
          key: authKey,
          lat: coords.lat,
          lng: coords.lng,
          check_in_rem: remarks || 'Checked in via App',
          check_in_address: address,
          check_in_date: formattedDate,
        });
        setLoading(false);
        showAlert('Success', 'Checked in successfully!', () => {
          if (navigation.canGoBack()) {
            navigation.goBack();
          } else {
            navigation.navigate('MainApp');
          }
        });
      } else {
        await ApiService.employeeCheckOut({
          key: authKey,
          lat: coords.lat,
          lng: coords.lng,
          check_out_rem: remarks || 'Checked out via App',
          check_out_address: address,
          check_in_date: formattedDate,
          check_out_date: formattedDate,
        });
        setLoading(false);
        showAlert('Success', 'Checked out successfully!', () => {
          if (navigation.canGoBack()) {
            navigation.goBack();
          } else {
            navigation.navigate('MainApp');
          }
        });
      }
    } catch (e: any) {
      setLoading(false);
      showAlert('Submission Error', e?.message || 'Failed to submit attendance.');
    }
  };

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      <HeaderBar
        title={isCheckIn ? 'Attendance Check In' : 'Attendance Check Out'}
        showBack
        onBackPress={() => navigation.goBack()}
      />

      <ScrollView contentContainerStyle={styles.scrollContent}>
        {/* Status Card */}
        <View style={styles.card}>
          <View style={styles.cardHeader}>
            <Ionicons
              name={isCheckIn ? 'log-in-outline' : 'log-out-outline'}
              size={28}
              color={isCheckIn ? Colors.success : '#DC2626'}
            />
            <Text style={styles.cardTitle}>
              {isCheckIn ? 'Punch In Attendance' : 'Punch Out Attendance'}
            </Text>
          </View>

          <View style={styles.divider} />

          {/* Time & Date */}
          <View style={styles.infoRow}>
            <Ionicons name="calendar-outline" size={20} color={Colors.grey} />
            <Text style={styles.infoText}>
              {new Date().toLocaleDateString('en-US', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
              })}
            </Text>
          </View>

          {/* GPS Coordinates */}
          <View style={styles.infoRow}>
            <Ionicons name="navigate-outline" size={20} color={Colors.grey} />
            {fetchingLocation ? (
              <View style={styles.loadingRow}>
                <ActivityIndicator size="small" color={Colors.primary} />
                <Text style={styles.loadingText}>Fetching GPS coordinates...</Text>
              </View>
            ) : coords ? (
              <Text style={styles.infoText}>
                Lat: {coords.lat.toFixed(5)}, Lng: {coords.lng.toFixed(5)}
              </Text>
            ) : (
              <Text style={[styles.infoText, { color: '#DC2626' }]}>
                Location not available
              </Text>
            )}
          </View>

          {/* Detected Address */}
          <View style={[styles.infoRow, { alignItems: 'flex-start' }]}>
            <Ionicons
              name="location-outline"
              size={20}
              color={Colors.primary}
              style={{ marginTop: 2 }}
            />
            <Text style={styles.addressText}>
              {address || 'Detecting address...'}
            </Text>
          </View>

          {/* Refresh Location Button */}
          <TouchableOpacity
            style={styles.refreshLocBtn}
            onPress={getCurrentLocation}
          >
            <Ionicons name="refresh" size={16} color={Colors.primary} />
            <Text style={styles.refreshLocText}>Refresh Location</Text>
          </TouchableOpacity>
        </View>

        {/* Remarks Input */}
        <View style={styles.card}>
          <Text style={styles.inputLabel}>Remarks / Notes (Optional)</Text>
          <TextInput
            style={styles.textArea}
            placeholder="Add any remarks for today's attendance..."
            placeholderTextColor="#999"
            multiline
            numberOfLines={4}
            value={remarks}
            onChangeText={setRemarks}
            textAlignVertical="top"
          />

          {/* GPS Guard Status Banner */}
          {fetchingLocation ? (
            <View style={styles.gpsStatusBanner}>
              <ActivityIndicator size="small" color={Colors.primary} />
              <Text style={styles.gpsStatusText}>Acquiring GPS location... Please wait</Text>
            </View>
          ) : !coords ? (
            <TouchableOpacity style={styles.gpsRetryBanner} onPress={getCurrentLocation}>
              <Ionicons name="alert-circle" size={18} color={Colors.danger} />
              <Text style={styles.gpsRetryText}>GPS unavailable. Tap to retry acquiring location.</Text>
            </TouchableOpacity>
          ) : (
            <View style={styles.gpsReadyBanner}>
              <Ionicons name="checkmark-circle" size={18} color={Colors.success} />
              <Text style={styles.gpsReadyText}>GPS Location Verified</Text>
            </View>
          )}

          <AppButton
            title={isCheckIn ? 'CONFIRM CHECK IN' : 'CONFIRM CHECK OUT'}
            onPress={handleSubmit}
            loading={loading}
            loadingText={isCheckIn ? 'Punching In...' : 'Punching Out...'}
            disabled={!coords || fetchingLocation || loading}
            variant={isCheckIn ? 'success' : 'danger'}
            size="large"
            style={{ marginTop: 20 }}
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
    padding: 16,
  },
  card: {
    backgroundColor: Colors.white,
    borderRadius: 14,
    padding: 18,
    marginBottom: 16,
    elevation: 2,
    shadowColor: '#000',
    shadowOffset: { width: 0, height: 1 },
    shadowOpacity: 0.1,
    shadowRadius: 3,
  },
  cardHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    marginBottom: 12,
  },
  cardTitle: {
    fontSize: 17,
    fontWeight: '700',
    color: Colors.black,
    marginLeft: 10,
  },
  divider: {
    height: 1,
    backgroundColor: '#EFEFEF',
    marginVertical: 10,
  },
  infoRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginVertical: 8,
  },
  infoText: {
    fontSize: 14,
    color: Colors.black,
    marginLeft: 10,
    fontWeight: '500',
  },
  addressText: {
    fontSize: 14,
    color: '#374151',
    marginLeft: 10,
    flex: 1,
    lineHeight: 20,
  },
  loadingRow: {
    flexDirection: 'row',
    alignItems: 'center',
    marginLeft: 10,
  },
  loadingText: {
    fontSize: 13,
    color: Colors.grey,
    marginLeft: 8,
  },
  refreshLocBtn: {
    flexDirection: 'row',
    alignItems: 'center',
    alignSelf: 'flex-start',
    marginTop: 12,
    paddingVertical: 6,
    paddingHorizontal: 12,
    borderRadius: 6,
    backgroundColor: `${Colors.primary}10`,
  },
  refreshLocText: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.primary,
    marginLeft: 6,
  },
  inputLabel: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.black,
    marginBottom: 8,
  },
  textArea: {
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    borderRadius: 10,
    padding: 12,
    fontSize: 14,
    color: Colors.black,
    minHeight: 90,
    backgroundColor: '#FAFAFA',
  },
  submitBtn: {
    borderRadius: 10,
    paddingVertical: 14,
    alignItems: 'center',
    marginTop: 18,
    elevation: 2,
  },
  submitBtnText: {
    color: Colors.white,
    fontSize: 15,
    fontWeight: '700',
    letterSpacing: 0.8,
  },
  gpsStatusBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FEF3C7',
    padding: 10,
    borderRadius: 8,
    marginTop: 14,
    gap: 8,
  },
  gpsStatusText: {
    fontSize: 13,
    color: '#B45309',
    fontWeight: '600',
    flex: 1,
  },
  gpsRetryBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#FEE2E2',
    padding: 10,
    borderRadius: 8,
    marginTop: 14,
    gap: 8,
  },
  gpsRetryText: {
    fontSize: 13,
    color: '#B91C1C',
    fontWeight: '600',
    flex: 1,
  },
  gpsReadyBanner: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#DCFCE7',
    padding: 10,
    borderRadius: 8,
    marginTop: 14,
    gap: 8,
  },
  gpsReadyText: {
    fontSize: 13,
    color: '#15803D',
    fontWeight: '600',
    flex: 1,
  },
});
