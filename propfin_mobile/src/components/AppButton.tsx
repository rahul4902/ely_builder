import React from 'react';
import {
  TouchableOpacity,
  Text,
  ActivityIndicator,
  StyleSheet,
  View,
  StyleProp,
  ViewStyle,
  TextStyle,
} from 'react-native';
import { Colors } from '../constants/colors';

export interface AppButtonProps {
  title: string;
  onPress: () => void;
  loading?: boolean;
  loadingText?: string;
  disabled?: boolean;
  variant?: 'primary' | 'secondary' | 'success' | 'danger' | 'outline';
  size?: 'small' | 'medium' | 'large';
  icon?: React.ReactNode;
  iconPosition?: 'left' | 'right';
  style?: StyleProp<ViewStyle>;
  textStyle?: StyleProp<TextStyle>;
  fullWidth?: boolean;
}

export const AppButton: React.FC<AppButtonProps> = ({
  title,
  onPress,
  loading = false,
  loadingText,
  disabled = false,
  variant = 'primary',
  size = 'medium',
  icon,
  iconPosition = 'left',
  style,
  textStyle,
  fullWidth = true,
}) => {
  const isDisabled = disabled || loading;

  const getVariantStyles = (): {
    container: ViewStyle;
    text: TextStyle;
    spinnerColor: string;
  } => {
    switch (variant) {
      case 'success':
        return {
          container: {
            backgroundColor: Colors.success,
            borderColor: Colors.success,
          },
          text: { color: Colors.white },
          spinnerColor: Colors.white,
        };
      case 'danger':
        return {
          container: {
            backgroundColor: Colors.danger,
            borderColor: Colors.danger,
          },
          text: { color: Colors.white },
          spinnerColor: Colors.white,
        };
      case 'outline':
        return {
          container: {
            backgroundColor: 'transparent',
            borderColor: Colors.primary,
            borderWidth: 1.5,
          },
          text: { color: Colors.primary },
          spinnerColor: Colors.primary,
        };
      case 'secondary':
        return {
          container: {
            backgroundColor: '#F1F5F9',
            borderColor: Colors.borderGrey,
            borderWidth: 1,
          },
          text: { color: Colors.bodyText },
          spinnerColor: Colors.bodyText,
        };
      case 'primary':
      default:
        return {
          container: {
            backgroundColor: Colors.primary,
            borderColor: Colors.primary,
          },
          text: { color: Colors.white },
          spinnerColor: Colors.white,
        };
    }
  };

  const getSizeStyles = (): {
    container: ViewStyle;
    text: TextStyle;
    spinnerSize: number | 'small' | 'large';
  } => {
    switch (size) {
      case 'small':
        return {
          container: { paddingVertical: 8, paddingHorizontal: 14, borderRadius: 8 },
          text: { fontSize: 13, fontWeight: '600' },
          spinnerSize: 'small',
        };
      case 'large':
        return {
          container: { paddingVertical: 16, paddingHorizontal: 24, borderRadius: 14 },
          text: { fontSize: 16, fontWeight: '700', letterSpacing: 0.5 },
          spinnerSize: 'small',
        };
      case 'medium':
      default:
        return {
          container: { paddingVertical: 12, paddingHorizontal: 20, borderRadius: 10 },
          text: { fontSize: 15, fontWeight: '700', letterSpacing: 0.3 },
          spinnerSize: 'small',
        };
    }
  };

  const variantStyles = getVariantStyles();
  const sizeStyles = getSizeStyles();

  const displayText = loading ? loadingText || title : title;

  return (
    <TouchableOpacity
      style={[
        styles.baseButton,
        variantStyles.container,
        sizeStyles.container,
        fullWidth && styles.fullWidth,
        isDisabled && styles.disabledButton,
        style,
      ]}
      onPress={onPress}
      disabled={isDisabled}
      activeOpacity={0.75}
    >
      <View style={styles.contentRow}>
        {loading ? (
          <ActivityIndicator
            size={sizeStyles.spinnerSize}
            color={variantStyles.spinnerColor}
            style={styles.spinner}
          />
        ) : (
          icon && iconPosition === 'left' && <View style={styles.iconLeft}>{icon}</View>
        )}

        <Text
          style={[
            styles.baseText,
            variantStyles.text,
            sizeStyles.text,
            isDisabled && !loading && styles.disabledText,
            textStyle,
          ]}
          numberOfLines={1}
        >
          {displayText}
        </Text>

        {!loading && icon && iconPosition === 'right' && (
          <View style={styles.iconRight}>{icon}</View>
        )}
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  baseButton: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.12,
    shadowRadius: 4,
  },
  fullWidth: {
    width: '100%',
  },
  contentRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
  },
  baseText: {
    textAlign: 'center',
  },
  spinner: {
    marginRight: 8,
  },
  iconLeft: {
    marginRight: 8,
  },
  iconRight: {
    marginLeft: 8,
  },
  disabledButton: {
    opacity: 0.55,
    elevation: 0,
    shadowOpacity: 0,
  },
  disabledText: {
    opacity: 0.85,
  },
});
