import React, { useEffect, useRef } from 'react';
import { View, StyleSheet, Image, Text, Animated, Easing, StatusBar } from 'react-native';
import { NativeStackNavigationProp } from '@react-navigation/native-stack';
import { Colors } from '../../constants/colors';
import { useAuth } from '../../context/AuthContext';
import { RootStackParamList } from '../../types';

type SplashScreenProps = {
  navigation: NativeStackNavigationProp<RootStackParamList, 'Splash'>;
};

export const SplashScreen = ({ navigation }: SplashScreenProps) => {
  const { authKey, isLoading } = useAuth();
  const scaleAnim = useRef(new Animated.Value(0.7)).current;
  const opacityAnim = useRef(new Animated.Value(0)).current;
  const taglineOpacity = useRef(new Animated.Value(0)).current;

  useEffect(() => {
    // Animate logo in
    Animated.parallel([
      Animated.spring(scaleAnim, {
        toValue: 1,
        tension: 60,
        friction: 8,
        useNativeDriver: true,
      }),
      Animated.timing(opacityAnim, {
        toValue: 1,
        duration: 600,
        easing: Easing.out(Easing.quad),
        useNativeDriver: true,
      }),
    ]).start(() => {
      // Fade in tagline after logo appears
      Animated.timing(taglineOpacity, {
        toValue: 1,
        duration: 500,
        useNativeDriver: true,
      }).start();
    });
  }, []);

  useEffect(() => {
    if (!isLoading) {
      const timer = setTimeout(() => {
        if (authKey) {
          navigation.replace('MainApp');
        } else {
          navigation.replace('Login');
        }
      }, 2200);
      return () => clearTimeout(timer);
    }
  }, [isLoading, authKey, navigation]);

  return (
    <View style={styles.container}>
      <StatusBar backgroundColor={Colors.primary} barStyle="light-content" />
      {/* Background circles for depth */}
      <View style={styles.bgCircle1} />
      <View style={styles.bgCircle2} />
      
      <Animated.View
        style={[
          styles.logoBox,
          {
            opacity: opacityAnim,
            transform: [{ scale: scaleAnim }],
          },
        ]}
      >
        <View style={styles.logoContainer}>
          <Image
            source={require('../../../assets/logo.png')}
            style={styles.logo}
            resizeMode="contain"
          />
        </View>
        <Text style={styles.appName}>ElyLeads</Text>
        <Animated.Text style={[styles.tagline, { opacity: taglineOpacity }]}>
          Real Estate CRM & Lead Management
        </Animated.Text>
      </Animated.View>

      <Animated.View style={[styles.bottomSection, { opacity: taglineOpacity }]}>
        <View style={styles.dotsRow}>
          <View style={[styles.dot, styles.dotActive]} />
          <View style={styles.dot} />
          <View style={styles.dot} />
        </View>
        <Text style={styles.loadingText}>Loading...</Text>
      </Animated.View>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    flex: 1,
    backgroundColor: Colors.primary,
    justifyContent: 'center',
    alignItems: 'center',
  },
  bgCircle1: {
    position: 'absolute',
    width: 400,
    height: 400,
    borderRadius: 200,
    backgroundColor: 'rgba(255, 255, 255, 0.06)',
    top: -100,
    right: -100,
  },
  bgCircle2: {
    position: 'absolute',
    width: 300,
    height: 300,
    borderRadius: 150,
    backgroundColor: 'rgba(255, 255, 255, 0.06)',
    bottom: -60,
    left: -80,
  },
  logoBox: {
    alignItems: 'center',
  },
  logoContainer: {
    width: 120,
    height: 120,
    borderRadius: 30,
    backgroundColor: 'rgba(255, 255, 255, 0.18)',
    justifyContent: 'center',
    alignItems: 'center',
    marginBottom: 22,
    borderWidth: 1.5,
    borderColor: 'rgba(255, 255, 255, 0.25)',
  },
  logo: {
    width: 88,
    height: 88,
  },
  appName: {
    fontSize: 30,
    fontWeight: '800',
    color: Colors.white,
    letterSpacing: 1.5,
    marginBottom: 8,
  },
  tagline: {
    fontSize: 14,
    color: 'rgba(255, 255, 255, 0.75)',
    fontWeight: '500',
    letterSpacing: 0.3,
  },
  bottomSection: {
    position: 'absolute',
    bottom: 60,
    alignItems: 'center',
  },
  dotsRow: {
    flexDirection: 'row',
    marginBottom: 10,
  },
  dot: {
    width: 6,
    height: 6,
    borderRadius: 3,
    backgroundColor: 'rgba(255, 255, 255, 0.35)',
    marginHorizontal: 3,
  },
  dotActive: {
    backgroundColor: Colors.white,
    width: 18,
  },
  loadingText: {
    fontSize: 12,
    color: 'rgba(255, 255, 255, 0.6)',
    fontWeight: '600',
    letterSpacing: 1,
    textTransform: 'uppercase',
  },
});
