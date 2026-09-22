import React from 'react';
import { createNativeStackNavigator } from '@react-navigation/native-stack';
import { SplashScreen } from '../screens/auth/SplashScreen';
import { LoginScreen } from '../screens/auth/LoginScreen';
import { OtpVerificationScreen } from '../screens/auth/OtpVerificationScreen';
import { BottomTabNavigator } from './BottomTabNavigator';
import { NewLeadScreen } from '../screens/leads/NewLeadScreen';
import { LeadListScreen } from '../screens/leads/LeadListScreen';
import { LeadDetailScreen } from '../screens/leads/LeadDetailScreen';
import { FollowUpScreen } from '../screens/leads/FollowUpScreen';
import { CheckInOutScreen } from '../screens/dashboard/CheckInOutScreen';
import { NotificationScreen } from '../screens/notifications/NotificationScreen';
import { NotificationSoundScreen } from '../screens/profile/NotificationSoundScreen';
import { RootStackParamList } from '../types';

const Stack = createNativeStackNavigator<RootStackParamList>();

export const RootNavigator = () => {
  return (
    <Stack.Navigator
      initialRouteName="Splash"
      screenOptions={{
        headerShown: false,
        animation: 'slide_from_right',
      }}
    >
      {/* Auth Flows */}
      <Stack.Screen name="Splash" component={SplashScreen} />
      <Stack.Screen name="Login" component={LoginScreen} />
      <Stack.Screen name="OtpVerification" component={OtpVerificationScreen} />

      {/* Main App Container */}
      <Stack.Screen name="MainApp" component={BottomTabNavigator} />

      {/* Feature Screens */}
      <Stack.Screen name="NewLead" component={NewLeadScreen} />
      <Stack.Screen name="LeadList" component={LeadListScreen} />
      <Stack.Screen name="LeadDetail" component={LeadDetailScreen} />
      <Stack.Screen name="FollowUp" component={FollowUpScreen} />
      <Stack.Screen name="CheckInOut" component={CheckInOutScreen} />
      <Stack.Screen name="Notifications" component={NotificationScreen} />
      <Stack.Screen
        name="NotificationSoundSetting"
        component={NotificationSoundScreen}
      />
    </Stack.Navigator>
  );
};
