# Profin Solution Mobile App (Android & iOS)

A cross-platform mobile application built with **React Native, Expo (SDK 57), and TypeScript**, replicating 100% of the features and design of the native Android application (`profin_solution_new_theme`) while running seamlessly on both **Android** and **iOS**.

---

## 📱 Features

- **Authentication**:
  - Registered 10-digit mobile number login via `API/login.php`.
  - OTP verification screen with 60-second resend timer and session persistence (`AsyncStorage`).
  - Splash screen with session check and auto-routing.
- **Home Shell**:
  - **4 Bottom Tabs**:
    1. **Home**: Leads dashboard & status pagers.
    2. **Calendar**: Follow-ups timeline with week selector and direct call triggers.
    3. **Call Logs**: Backend call history with duration and direct redial.
    4. **Profile**: User info, notification preferences, and logout.
  - **Top App Bar**: Profin Solution logo, title, notification bell with live unread badge, and quick logout.
- **Dashboard & Leads Pager (6 Top Horizontal Tabs)**:
  - **Home (Dashboard)**:
    - Welcome greeting & daily date banner.
    - Real-time GPS Attendance Check-In / Check-Out card.
    - Pending follow-ups counter card.
    - 4 Lead overview metric cards (Total Leads, Lead Open, Lead In Process, Lead Closed).
    - 4 Today's activity metric cards (Today Total, Pending, Today Activity, Today Closed).
    - Floating Action Button (FAB) with smooth 360° rotation opening New Lead form.
  - **New Leads**: Filtered leads from `API/Leads_Filter.php` with `status: new`.
  - **Interested Leads**: Filtered leads with `status: interested`.
  - **Meetings Done**: Filtered leads with `status: meeting`.
  - **Visit Done**: Filtered leads with `status: visit`.
  - **Booking Done**: Filtered leads with `status: booked`.
- **Direct Lead Actions on Every Card**:
  - 📄 **Detail**: Opens comprehensive Lead Details screen.
  - 📅 **Follow-Up**: Opens schedule follow-up screen.
  - 💬 **WhatsApp**: Direct chat via `https://wa.me/91<number>`.
  - 📞 **Call**: Direct phone dialing with automatic call logging (`API/addcalllogs.php`).
- **Lead Operations**:
  - **New Lead Form**: Full client capture with dynamic master data (Projects, Requirements, Budgets, Sources) from `API/master.php` plus custom "Other" inputs.
  - **Lead List Screen**: Opened from any dashboard metric card with live search filter.
  - **Lead Detail Screen**: Full client information, previous follow-up timeline, and action toolbar.
  - **Add Follow-Up**: Status selection (In Progress, Closed, Not Valid, Broker, Not Interested), next date/time pickers, and comments.
  - **Meeting Done Modal**: Log meeting completion with Senior selection, meeting type, and follow-up status.
- **Attendance (Check-In / Check-Out)**:
  - High-accuracy GPS coordinates via `expo-location`.
  - Reverse geocoding to formatted street address.
  - Custom remarks input.
  - Automated status detection from `API/check_checkin.php`.
- **Notifications & Preferences**:
  - In-app notification center from `API/usernotifications.php`.
  - Notification mark-read sync via `API/notification_read.php`.
  - Notification sound, vibration, and push preferences.

---

## 🛠️ Tech Stack & Mapping to Android

| Component / Feature | Reference Android App | New Cross-Platform App |
|---|---|---|
| **Framework** | Native Java / Android SDK | React Native (Expo SDK 57, TypeScript) |
| **Styling** | XML layouts (`colors.xml`, `drawables/`) | React Native StyleSheet (`Colors` theme `#A63130`) |
| **API Client** | Retrofit 2 + OkHttpClient | Axios with form-urlencoded interceptors |
| **Storage** | `SharedPreferences` | `@react-native-async-storage/async-storage` |
| **Navigation** | `BottomNavigationView` + `ViewPager2` | React Navigation (Bottom Tabs + Material Top Tabs + Native Stack) |
| **Location / GPS** | `FusedLocationProviderClient` + `Geocoder` | `expo-location` (Location + reverse geocode) |
| **Phone & WhatsApp** | `Intent.ACTION_CALL` + WhatsApp intent | `expo-linking` (`tel:` & `https://wa.me/`) |

---

## 🚀 Getting Started

### 1. Prerequisites
- **Node.js**: v18 or higher (v24 is installed and verified).
- **npm**: v9 or higher.
- **Expo Go App**: Download "Expo Go" on your iOS device (App Store) or Android device (Google Play Store) to preview instantly on physical hardware.

### 2. Start the Development Server
Navigate into the `propfin_mobile` directory and run:

```bash
cd propfin_mobile
npx expo start
```

- **For Android Device**: Scan the QR code displayed in the terminal using the Expo Go app on Android.
- **For iOS Device**: Scan the QR code using the iPhone Camera app and open in Expo Go.
- **For Web Preview**: Press `w` in the terminal to run in your local web browser.

### 3. Build Standalone App (.apk / .aab / .ipa)
To build standalone release packages for distribution:
```bash
# Install EAS CLI globally
npm install -g eas-cli

# Login to Expo
eas login

# Configure build
eas build:configure

# Build Android APK (for direct install on devices)
eas build --platform android --profile preview

# Build iOS IPA
eas build --platform ios --profile preview
```
