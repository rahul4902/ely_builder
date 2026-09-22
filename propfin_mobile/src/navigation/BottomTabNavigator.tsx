import React from 'react';
import { View, StyleSheet } from 'react-native';
import { createBottomTabNavigator } from '@react-navigation/bottom-tabs';
import { useNavigation } from '@react-navigation/native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/colors';
import { HeaderBar } from '../components/HeaderBar';
import { HomeTopTabNavigator } from './HomeTopTabNavigator';
import { CalendarScreen } from '../screens/calendar/CalendarScreen';
import { CallLogsScreen } from '../screens/calllogs/CallLogsScreen';
import { ProfileScreen } from '../screens/profile/ProfileScreen';
import { BottomTabParamList, RootStackParamList } from '../types';

const Tab = createBottomTabNavigator<BottomTabParamList>();

export const BottomTabNavigator = () => {
  const rootNavigation = useNavigation<NativeStackNavigationProp<RootStackParamList>>();

  return (
    <View style={styles.container}>
      <HeaderBar
        onNotificationPress={() => rootNavigation.navigate('Notifications')}
      />

      <Tab.Navigator
        screenOptions={({ route }) => ({
          headerShown: false,
          tabBarActiveTintColor: Colors.primary,
          tabBarInactiveTintColor: Colors.grey,
          tabBarStyle: {
            height: 62,
            paddingBottom: 10,
            paddingTop: 6,
            backgroundColor: Colors.white,
            borderTopWidth: 1,
            borderTopColor: '#EAEAEA',
            elevation: 10,
            shadowColor: '#000',
            shadowOffset: { width: 0, height: -3 },
            shadowOpacity: 0.1,
            shadowRadius: 4,
          },
          tabBarLabelStyle: {
            fontSize: 10,
            fontWeight: '700',
          },
          tabBarIcon: ({ color, focused, size }) => {
            let iconName: keyof typeof Ionicons.glyphMap = 'home';
            if (route.name === 'HomeTab') {
              iconName = focused ? 'home' : 'home-outline';
            } else if (route.name === 'CalendarTab') {
              iconName = focused ? 'calendar' : 'calendar-outline';
            } else if (route.name === 'CallLogsTab') {
              iconName = focused ? 'call' : 'call-outline';
            } else if (route.name === 'ProfileTab') {
              iconName = focused ? 'person' : 'person-outline';
            }
            return <Ionicons name={iconName} size={22} color={color} />;
          },
        })}
      >
        <Tab.Screen
          name="HomeTab"
          component={HomeTopTabNavigator}
          options={{ tabBarLabel: 'Home' }}
        />
        <Tab.Screen
          name="CalendarTab"
          component={CalendarScreen}
          options={{ tabBarLabel: 'Calendar' }}
        />
        <Tab.Screen
          name="CallLogsTab"
          component={CallLogsScreen}
          options={{ tabBarLabel: 'Call Logs' }}
        />
        <Tab.Screen
          name="ProfileTab"
          component={ProfileScreen}
          options={{ tabBarLabel: 'Profile' }}
        />
      </Tab.Navigator>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.background,
  },
});
