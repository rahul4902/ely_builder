import { Linking, Alert } from 'react-native';

export const AppLinking = {
  async openWhatsApp(contactNo: string): Promise<void> {
    const cleanNumber = contactNo.replace(/\D/g, '');
    if (!cleanNumber) {
      Alert.alert('Invalid Number', 'No valid contact number found.');
      return;
    }
    const fullNumber = cleanNumber.length === 10 ? `91${cleanNumber}` : cleanNumber;
    const url = `https://wa.me/${fullNumber}`;

    try {
      const supported = await Linking.canOpenURL(url);
      if (supported) {
        await Linking.openURL(url);
      } else {
        await Linking.openURL(url);
      }
    } catch {
      Alert.alert('Error', 'Unable to open WhatsApp on this device.');
    }
  },

  async makePhoneCall(
    contactNo: string,
    leadId?: string,
    onCallInitiated?: (startTime: Date) => void
  ): Promise<void> {
    const cleanNumber = contactNo.replace(/\D/g, '');
    if (!cleanNumber) {
      Alert.alert('Invalid Number', 'No valid phone number found.');
      return;
    }
    const url = `tel:${cleanNumber}`;

    try {
      const startTime = new Date();
      if (onCallInitiated) {
        onCallInitiated(startTime);
      }
      await Linking.openURL(url);
    } catch {
      Alert.alert('Error', 'Unable to initiate phone call on this device.');
    }
  },
};
