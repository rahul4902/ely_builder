import React, { useEffect, useRef } from 'react';
import { View, StyleSheet, Animated } from 'react-native';
import { Colors } from '../constants/colors';

export const SkeletonBox = ({
  width,
  height,
  borderRadius = 8,
  style,
}: {
  width: number | string;
  height: number | string;
  borderRadius?: number;
  style?: any;
}) => {
  const opacityAnim = useRef(new Animated.Value(0.35)).current;

  useEffect(() => {
    const pulse = Animated.loop(
      Animated.sequence([
        Animated.timing(opacityAnim, {
          toValue: 0.8,
          duration: 700,
          useNativeDriver: true,
        }),
        Animated.timing(opacityAnim, {
          toValue: 0.35,
          duration: 700,
          useNativeDriver: true,
        }),
      ])
    );
    pulse.start();
    return () => pulse.stop();
  }, [opacityAnim]);

  return (
    <Animated.View
      style={[
        styles.skeletonBase,
        {
          width: width as any,
          height: height as any,
          borderRadius,
          opacity: opacityAnim,
        },
        style,
      ]}
    />
  );
};

export const DashboardSkeleton = () => {
  return (
    <View style={styles.container}>
      {/* Welcome Banner Skeleton */}
      <View style={styles.welcomeBanner}>
        <SkeletonBox width={140} height={20} borderRadius={6} />
        <SkeletonBox width={180} height={14} borderRadius={4} style={{ marginTop: 8 }} />
      </View>

      {/* Attendance Card Skeleton */}
      <View style={styles.card}>
        <View style={styles.rowBetween}>
          <View style={styles.row}>
            <SkeletonBox width={36} height={36} borderRadius={18} />
            <View style={{ marginLeft: 12 }}>
              <SkeletonBox width={120} height={16} borderRadius={4} />
              <SkeletonBox width={160} height={12} borderRadius={4} style={{ marginTop: 6 }} />
            </View>
          </View>
          <SkeletonBox width={85} height={34} borderRadius={8} />
        </View>
      </View>

      {/* Pending Follow-Up Card Skeleton */}
      <View style={[styles.card, styles.followUpCard]}>
        <View style={styles.rowBetween}>
          <View style={styles.row}>
            <SkeletonBox width={40} height={40} borderRadius={20} style={styles.whiteSkeleton} />
            <View style={{ marginLeft: 12 }}>
              <SkeletonBox width={50} height={22} borderRadius={4} style={styles.whiteSkeleton} />
              <SkeletonBox width={130} height={13} borderRadius={4} style={[styles.whiteSkeleton, { marginTop: 6 }]} />
            </View>
          </View>
          <SkeletonBox width={24} height={24} borderRadius={12} style={styles.whiteSkeleton} />
        </View>
      </View>

      {/* Overview Section Header */}
      <SkeletonBox width={90} height={18} borderRadius={4} style={{ marginVertical: 12, marginLeft: 4 }} />

      {/* 2x2 Metric Grid Skeleton */}
      <View style={styles.gridRow}>
        <View style={styles.metricCard}>
          <SkeletonBox width={36} height={36} borderRadius={18} />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <SkeletonBox width={40} height={20} borderRadius={4} />
            <SkeletonBox width={70} height={12} borderRadius={4} style={{ marginTop: 6 }} />
          </View>
        </View>
        <View style={styles.metricCard}>
          <SkeletonBox width={36} height={36} borderRadius={18} />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <SkeletonBox width={40} height={20} borderRadius={4} />
            <SkeletonBox width={70} height={12} borderRadius={4} style={{ marginTop: 6 }} />
          </View>
        </View>
      </View>

      <View style={styles.gridRow}>
        <View style={styles.metricCard}>
          <SkeletonBox width={36} height={36} borderRadius={18} />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <SkeletonBox width={40} height={20} borderRadius={4} />
            <SkeletonBox width={70} height={12} borderRadius={4} style={{ marginTop: 6 }} />
          </View>
        </View>
        <View style={styles.metricCard}>
          <SkeletonBox width={36} height={36} borderRadius={18} />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <SkeletonBox width={40} height={20} borderRadius={4} />
            <SkeletonBox width={70} height={12} borderRadius={4} style={{ marginTop: 6 }} />
          </View>
        </View>
      </View>

      {/* Today Section Header */}
      <SkeletonBox width={120} height={18} borderRadius={4} style={{ marginVertical: 12, marginLeft: 4, marginTop: 18 }} />

      <View style={styles.gridRow}>
        <View style={styles.metricCard}>
          <SkeletonBox width={36} height={36} borderRadius={18} />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <SkeletonBox width={40} height={20} borderRadius={4} />
            <SkeletonBox width={70} height={12} borderRadius={4} style={{ marginTop: 6 }} />
          </View>
        </View>
        <View style={styles.metricCard}>
          <SkeletonBox width={36} height={36} borderRadius={18} />
          <View style={{ marginLeft: 10, flex: 1 }}>
            <SkeletonBox width={40} height={20} borderRadius={4} />
            <SkeletonBox width={70} height={12} borderRadius={4} style={{ marginTop: 6 }} />
          </View>
        </View>
      </View>
    </View>
  );
};

export const LeadListSkeleton = ({ count = 4 }: { count?: number }) => {
  return (
    <View style={styles.listContainer}>
      {Array.from({ length: count }).map((_, index) => (
        <View key={index} style={styles.leadCardSkeleton}>
          <View style={styles.leadInfoSkeleton}>
            <SkeletonBox width={140} height={18} borderRadius={4} />
            <SkeletonBox
              width={100}
              height={12}
              borderRadius={4}
              style={{ marginTop: 8 }}
            />
            <SkeletonBox
              width={170}
              height={12}
              borderRadius={4}
              style={{ marginTop: 6 }}
            />
            <SkeletonBox
              width={130}
              height={22}
              borderRadius={11}
              style={{ marginTop: 10 }}
            />
          </View>
          <View style={styles.leadActionsSkeleton}>
            <SkeletonBox width={34} height={34} borderRadius={17} style={{ marginBottom: 6 }} />
            <SkeletonBox width={34} height={34} borderRadius={17} style={{ marginBottom: 6 }} />
            <SkeletonBox width={34} height={34} borderRadius={17} style={{ marginBottom: 6 }} />
            <SkeletonBox width={34} height={34} borderRadius={17} />
          </View>
        </View>
      ))}
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    padding: 14,
  },
  skeletonBase: {
    backgroundColor: '#E2E8F0',
  },
  whiteSkeleton: {
    backgroundColor: 'rgba(255, 255, 255, 0.4)',
  },
  welcomeBanner: {
    backgroundColor: Colors.white,
    padding: 16,
    borderRadius: 14,
    marginBottom: 14,
    borderLeftWidth: 4,
    borderLeftColor: '#CBD5E1',
  },
  card: {
    backgroundColor: Colors.white,
    borderRadius: 14,
    padding: 16,
    marginBottom: 14,
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  followUpCard: {
    backgroundColor: Colors.primary,
    borderColor: Colors.primary,
  },
  rowBetween: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    alignItems: 'center',
  },
  row: {
    flexDirection: 'row',
    alignItems: 'center',
  },
  gridRow: {
    flexDirection: 'row',
    justifyContent: 'space-between',
    marginBottom: 8,
  },
  metricCard: {
    flex: 1,
    backgroundColor: Colors.white,
    borderRadius: 14,
    padding: 14,
    margin: 4,
    flexDirection: 'row',
    alignItems: 'center',
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  listContainer: {
    paddingVertical: 8,
  },
  leadCardSkeleton: {
    backgroundColor: Colors.white,
    borderRadius: 16,
    padding: 16,
    marginHorizontal: 14,
    marginVertical: 6,
    flexDirection: 'row',
    justifyContent: 'space-between',
    borderWidth: 1,
    borderColor: '#F1F5F9',
  },
  leadInfoSkeleton: {
    flex: 1,
    marginRight: 12,
  },
  leadActionsSkeleton: {
    flexDirection: 'column',
    alignItems: 'center',
  },
});
