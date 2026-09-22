import React from 'react';
import { View, Text, StyleSheet, TouchableOpacity } from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/colors';

interface MetricCardProps {
  title: string;
  count: number | string;
  iconName: keyof typeof Ionicons.glyphMap;
  color: string;
  onPress?: () => void;
}

export const MetricCard = ({
  title,
  count,
  iconName,
  color,
  onPress,
}: MetricCardProps) => {
  return (
    <TouchableOpacity
      style={styles.card}
      onPress={onPress}
      activeOpacity={0.7}
    >
      <View style={[styles.iconContainer, { backgroundColor: `${color}18` }]}>
        <Ionicons name={iconName} size={24} color={color} />
      </View>
      <View style={styles.textContainer}>
        <Text style={styles.countText}>{count ?? 0}</Text>
        <Text style={styles.titleText} numberOfLines={1}>
          {title}
        </Text>
      </View>
    </TouchableOpacity>
  );
};

const styles = StyleSheet.create({
  card: {
    flex: 1,
    minWidth: '46%',
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 14,
    margin: 5,
    flexDirection: 'row',
    alignItems: 'center',
    elevation: 2,
    shadowColor: '#0F172A',
    shadowOffset: { width: 0, height: 2 },
    shadowOpacity: 0.05,
    shadowRadius: 6,
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  iconContainer: {
    width: 44,
    height: 44,
    borderRadius: 14,
    justifyContent: 'center',
    alignItems: 'center',
    marginRight: 12,
  },
  textContainer: {
    flex: 1,
    justifyContent: 'center',
  },
  countText: {
    fontSize: 22,
    fontWeight: '800',
    color: Colors.textDark,
  },
  titleText: {
    fontSize: 12,
    fontWeight: '600',
    color: Colors.grey,
    marginTop: 2,
  },
});
