import React, { useState, useMemo, useCallback } from 'react';
import {
  View,
  Text,
  TextInput,
  TouchableOpacity,
  Modal,
  FlatList,
  StyleSheet,
  ActivityIndicator,
  KeyboardAvoidingView,
  Platform,
  SafeAreaView,
  ViewStyle,
} from 'react-native';
import { Ionicons } from '@expo/vector-icons';
import { Colors } from '../constants/colors';

const PAGE_SIZE = 30;

export interface SearchableDropdownProps {
  label?: string;
  placeholder?: string;
  items: string[];
  selectedValue: string;
  onSelect: (value: string) => void;
  allowCustomOther?: boolean;
  customOtherValue?: string;
  onChangeCustomOther?: (val: string) => void;
  customOtherPlaceholder?: string;
  modalTitle?: string;
  required?: boolean;
  style?: ViewStyle;
}

export const SearchableDropdown: React.FC<SearchableDropdownProps> = ({
  label,
  placeholder = 'Select an option...',
  items,
  selectedValue,
  onSelect,
  allowCustomOther = true,
  customOtherValue = '',
  onChangeCustomOther,
  customOtherPlaceholder = 'Enter custom value',
  modalTitle,
  required = false,
  style,
}) => {
  const [modalVisible, setModalVisible] = useState(false);
  const [searchQuery, setSearchQuery] = useState('');
  const [displayCount, setDisplayCount] = useState(PAGE_SIZE);

  const title = modalTitle || label || 'Select Option';

  // Filter items according to search query
  const filteredItems = useMemo(() => {
    if (!searchQuery.trim()) {
      return items;
    }
    const q = searchQuery.toLowerCase().trim();
    return items.filter((item) => item && item.toLowerCase().includes(q));
  }, [items, searchQuery]);

  // Paginated/Infinite slice of items
  const visibleItems = useMemo(() => {
    return filteredItems.slice(0, displayCount);
  }, [filteredItems, displayCount]);

  const handleOpen = () => {
    setSearchQuery('');
    setDisplayCount(PAGE_SIZE);
    setModalVisible(true);
  };

  const handleClose = () => {
    setModalVisible(false);
  };

  const handleSelectItem = (item: string) => {
    onSelect(item);
    setModalVisible(false);
  };

  const handleLoadMore = useCallback(() => {
    if (displayCount < filteredItems.length) {
      setDisplayCount((prev) => prev + PAGE_SIZE);
    }
  }, [displayCount, filteredItems.length]);

  const renderItem = ({ item }: { item: string }) => {
    const isSelected = selectedValue === item;
    return (
      <TouchableOpacity
        style={[styles.itemRow, isSelected && styles.itemRowSelected]}
        onPress={() => handleSelectItem(item)}
        activeOpacity={0.7}
      >
        <Text style={[styles.itemText, isSelected && styles.itemTextSelected]}>
          {item}
        </Text>
        {isSelected && (
          <Ionicons name="checkmark-circle" size={20} color={Colors.primary} />
        )}
      </TouchableOpacity>
    );
  };

  const hasMore = displayCount < filteredItems.length;

  return (
    <View style={[styles.container, style]}>
      {/* Field Label */}
      {label && (
        <Text style={styles.label}>
          {label} {required && <Text style={styles.requiredAsterisk}>*</Text>}
        </Text>
      )}

      {/* Trigger Box */}
      <TouchableOpacity
        style={[styles.triggerBox, modalVisible && styles.triggerBoxActive]}
        onPress={handleOpen}
        activeOpacity={0.8}
      >
        <Text
          style={[
            styles.triggerText,
            !selectedValue && styles.triggerPlaceholder,
          ]}
          numberOfLines={1}
        >
          {selectedValue || placeholder}
        </Text>
        <Ionicons
          name="chevron-down"
          size={18}
          color={Colors.grey}
          style={styles.chevron}
        />
      </TouchableOpacity>

      {/* Custom Other Input if "Other" is selected */}
      {allowCustomOther && selectedValue === 'Other' && onChangeCustomOther && (
        <TextInput
          style={styles.customInput}
          placeholder={customOtherPlaceholder}
          value={customOtherValue}
          onChangeText={onChangeCustomOther}
          placeholderTextColor={Colors.textMuted}
        />
      )}

      {/* Selection Modal with Infinite Scroll & Live Search */}
      <Modal
        visible={modalVisible}
        animationType="slide"
        transparent
        onRequestClose={handleClose}
      >
        <View style={styles.modalOverlay}>
          <KeyboardAvoidingView
            style={styles.keyboardAvoid}
            behavior={Platform.OS === 'ios' ? 'padding' : undefined}
          >
            <SafeAreaView style={styles.modalSheet}>
              {/* Modal Header */}
              <View style={styles.modalHeader}>
                <View style={styles.headerLeft}>
                  <Text style={styles.modalTitle}>{title}</Text>
                  <Text style={styles.recordBadge}>
                    {filteredItems.length} {filteredItems.length === 1 ? 'record' : 'records'}
                  </Text>
                </View>
                <TouchableOpacity
                  onPress={handleClose}
                  style={styles.closeBtn}
                  hitSlop={{ top: 10, bottom: 10, left: 10, right: 10 }}
                >
                  <Ionicons name="close" size={24} color={Colors.black} />
                </TouchableOpacity>
              </View>

              {/* Search Bar */}
              <View style={styles.searchContainer}>
                <Ionicons
                  name="search"
                  size={18}
                  color={Colors.grey}
                  style={styles.searchIcon}
                />
                <TextInput
                  style={styles.searchInput}
                  placeholder={'Search ' + title.toLowerCase() + '...'}
                  value={searchQuery}
                  onChangeText={(text) => {
                    setSearchQuery(text);
                    setDisplayCount(PAGE_SIZE);
                  }}
                  autoCorrect={false}
                  clearButtonMode="while-editing"
                  placeholderTextColor={Colors.textMuted}
                />
                {searchQuery.length > 0 && (
                  <TouchableOpacity
                    onPress={() => setSearchQuery('')}
                    style={styles.clearBtn}
                  >
                    <Ionicons name="close-circle" size={18} color={Colors.grey} />
                  </TouchableOpacity>
                )}
              </View>

              {/* Items List with Infinite Scroll */}
              <FlatList
                data={visibleItems}
                keyExtractor={(item, index) => item + '-' + index}
                renderItem={renderItem}
                keyboardShouldPersistTaps="handled"
                onEndReached={handleLoadMore}
                onEndReachedThreshold={0.4}
                initialNumToRender={PAGE_SIZE}
                maxToRenderPerBatch={PAGE_SIZE}
                windowSize={10}
                style={styles.list}
                contentContainerStyle={styles.listContent}
                ListFooterComponent={
                  hasMore ? (
                    <View style={styles.footerLoader}>
                      <ActivityIndicator size="small" color={Colors.primary} />
                      <Text style={styles.footerText}>
                        Showing {visibleItems.length} of {filteredItems.length}...
                      </Text>
                    </View>
                  ) : filteredItems.length > PAGE_SIZE ? (
                    <Text style={styles.endText}>All {filteredItems.length} items loaded</Text>
                  ) : null
                }
                ListEmptyComponent={
                  <View style={styles.emptyContainer}>
                    <Ionicons name="search-outline" size={42} color={Colors.borderGrey} />
                    <Text style={styles.emptyTitle}>No matching records found</Text>
                    <Text style={styles.emptySubtitle}>
                      Try searching with a different term
                    </Text>
                  </View>
                }
              />
            </SafeAreaView>
          </KeyboardAvoidingView>
        </View>
      </Modal>
    </View>
  );
};

const styles = StyleSheet.create({
  container: {
    marginBottom: 14,
  },
  label: {
    fontSize: 13,
    fontWeight: '600',
    color: Colors.black,
    marginBottom: 6,
  },
  requiredAsterisk: {
    color: Colors.primary,
  },
  triggerBox: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    backgroundColor: '#FAFAFA',
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 12,
  },
  triggerBoxActive: {
    borderColor: Colors.primary,
    backgroundColor: '#FFF',
  },
  triggerText: {
    flex: 1,
    fontSize: 14,
    color: Colors.black,
    fontWeight: '500',
  },
  triggerPlaceholder: {
    color: Colors.textMuted,
    fontWeight: '400',
  },
  chevron: {
    marginLeft: 8,
  },
  customInput: {
    marginTop: 8,
    backgroundColor: '#FAFAFA',
    borderWidth: 1,
    borderColor: Colors.borderGrey,
    borderRadius: 8,
    paddingHorizontal: 12,
    paddingVertical: 10,
    fontSize: 14,
    color: Colors.black,
  },
  modalOverlay: {
    flex: 1,
    backgroundColor: 'rgba(0, 0, 0, 0.5)',
    justifyContent: 'flex-end',
  },
  keyboardAvoid: {
    flex: 1,
    justifyContent: 'flex-end',
  },
  modalSheet: {
    backgroundColor: '#FFFFFF',
    borderTopLeftRadius: 20,
    borderTopRightRadius: 20,
    maxHeight: '85%',
    minHeight: '60%',
    shadowColor: '#000',
    shadowOffset: { width: 0, height: -3 },
    shadowOpacity: 0.15,
    shadowRadius: 10,
    elevation: 10,
  },
  modalHeader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingHorizontal: 18,
    paddingTop: 16,
    paddingBottom: 12,
    borderBottomWidth: 1,
    borderBottomColor: '#F0F0F0',
  },
  headerLeft: {
    flexDirection: 'row',
    alignItems: 'center',
    gap: 10,
  },
  modalTitle: {
    fontSize: 17,
    fontWeight: '700',
    color: Colors.black,
  },
  recordBadge: {
    fontSize: 11,
    fontWeight: '600',
    backgroundColor: '#F2F2F2',
    color: Colors.grey,
    paddingHorizontal: 8,
    paddingVertical: 3,
    borderRadius: 12,
  },
  closeBtn: {
    padding: 4,
  },
  searchContainer: {
    flexDirection: 'row',
    alignItems: 'center',
    backgroundColor: '#F5F5F7',
    marginHorizontal: 16,
    marginTop: 12,
    marginBottom: 8,
    paddingHorizontal: 12,
    borderRadius: 10,
    borderWidth: 1,
    borderColor: '#E8E8ED',
  },
  searchIcon: {
    marginRight: 8,
  },
  searchInput: {
    flex: 1,
    paddingVertical: 10,
    fontSize: 14,
    color: Colors.black,
  },
  clearBtn: {
    padding: 4,
  },
  list: {
    flex: 1,
  },
  listContent: {
    paddingHorizontal: 16,
    paddingBottom: 24,
  },
  itemRow: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'space-between',
    paddingVertical: 13,
    paddingHorizontal: 14,
    borderRadius: 8,
    marginVertical: 2,
    borderBottomWidth: StyleSheet.hairlineWidth,
    borderBottomColor: '#F0F0F0',
  },
  itemRowSelected: {
    backgroundColor: '#FFF1F0',
  },
  itemText: {
    flex: 1,
    fontSize: 14,
    color: Colors.black,
  },
  itemTextSelected: {
    fontWeight: '700',
    color: Colors.primary,
  },
  footerLoader: {
    flexDirection: 'row',
    alignItems: 'center',
    justifyContent: 'center',
    gap: 8,
    paddingVertical: 16,
  },
  footerText: {
    fontSize: 12,
    color: Colors.grey,
  },
  endText: {
    textAlign: 'center',
    fontSize: 11,
    color: Colors.grey,
    paddingVertical: 12,
  },
  emptyContainer: {
    alignItems: 'center',
    justifyContent: 'center',
    paddingVertical: 48,
    gap: 8,
  },
  emptyTitle: {
    fontSize: 15,
    fontWeight: '600',
    color: Colors.black,
    marginTop: 8,
  },
  emptySubtitle: {
    fontSize: 12,
    color: Colors.grey,
  },
});
