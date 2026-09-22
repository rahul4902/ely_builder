import { Alert, Platform } from 'react-native';

export const showAlert = (
  title: string,
  message?: string,
  onOk?: () => void
) => {
  if (Platform.OS === 'web') {
    const fullMsg = message ? `${title}\n\n${message}` : title;
    window.alert(fullMsg);
    if (onOk) {
      onOk();
    }
  } else {
    Alert.alert(title, message, [
      {
        text: 'OK',
        onPress: () => {
          if (onOk) onOk();
        },
      },
    ]);
  }
};
