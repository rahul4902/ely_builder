import React from 'react';
import { StyleSheet, View } from 'react-native';
import { createMaterialTopTabNavigator } from '@react-navigation/material-top-tabs';
import { Colors } from '../constants/colors';
import { DashboardScreen } from '../screens/dashboard/DashboardScreen';
import { LeadStatusTabScreen } from '../screens/leads/LeadStatusTabScreen';
import { HomeTopTabParamList } from '../types';

const Tab = createMaterialTopTabNavigator<HomeTopTabParamList>();

const NewLeadsTab = React.memo(() => <LeadStatusTabScreen statusFilter="new" />);
const InterestedLeadsTab = React.memo(() => <LeadStatusTabScreen statusFilter="interested" />);
const MeetingDoneLeadsTab = React.memo(() => <LeadStatusTabScreen statusFilter="meeting" />);
const VisitDoneLeadsTab = React.memo(() => <LeadStatusTabScreen statusFilter="visit" />);
const BookingDoneLeadsTab = React.memo(() => <LeadStatusTabScreen statusFilter="booked" />);

export const HomeTopTabNavigator = () => {
  return (
    <View style={styles.container}>
      <Tab.Navigator
        screenOptions={{
          lazy: true,
          tabBarScrollEnabled: true,
          tabBarActiveTintColor: Colors.primary,
          tabBarInactiveTintColor: Colors.grey,
          tabBarIndicatorStyle: {
            backgroundColor: Colors.primary,
            height: 3,
            borderRadius: 1.5,
          },
          tabBarLabelStyle: {
            fontSize: 13,
            fontWeight: '700',
            textTransform: 'none',
          },
          tabBarStyle: {
            backgroundColor: Colors.white,
            elevation: 2,
            shadowColor: '#000',
            shadowOffset: { width: 0, height: 1 },
            shadowOpacity: 0.1,
            shadowRadius: 2,
          },
          tabBarItemStyle: {
            width: 'auto',
            paddingHorizontal: 16,
          },
        }}
      >
        <Tab.Screen
          name="Dashboard"
          component={DashboardScreen}
          options={{ title: 'Home' }}
        />
        <Tab.Screen
          name="NewLeads"
          component={NewLeadsTab}
          options={{ title: 'New' }}
        />
        <Tab.Screen
          name="InterestedLeads"
          component={InterestedLeadsTab}
          options={{ title: 'Interested' }}
        />
        <Tab.Screen
          name="MeetingDoneLeads"
          component={MeetingDoneLeadsTab}
          options={{ title: 'Meetings Done' }}
        />
        <Tab.Screen
          name="VisitDoneLeads"
          component={VisitDoneLeadsTab}
          options={{ title: 'Visit Done' }}
        />
        <Tab.Screen
          name="BookingDoneLeads"
          component={BookingDoneLeadsTab}
          options={{ title: 'Booking Done' }}
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
