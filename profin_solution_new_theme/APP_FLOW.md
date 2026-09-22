# Profin Solution App Flow

This document describes the current Android navigation and screen behavior found in the Java/XML source code.

## 1. Application Type

- Platform: Native Android
- UI: Android XML layouts and Java Activities/Fragments
- Authentication: Firebase phone OTP plus the application login API
- Backend: HTTP APIs exposed through `ApiInterface`
- Main purpose: Lead management, lead follow-ups, employee attendance, notifications, and outgoing call tracking

## 2. Complete Navigation Map

```mermaid
flowchart TD
    Splash[SplashActivity\nStartup and permissions]
    Login[LoginActivity\nMobile number]
    OTP[OtpVerificationActivity\nFirebase OTP]
    Home[HomeActivity\nAuthenticated shell]

    Splash -->|Logged in| Home
    Splash -->|Not logged in| Login
    Login -->|Login API succeeds and code sent| OTP
    OTP -->|OTP verified and login API succeeds| Home

    Home --> Dashboard[HomeFragment\nDashboard pager]
    Home --> Calendar[CalendarFragment\nFollow-ups and calls]
    Home --> CallLogs[CallLogsFragment\nBackend call logs]
    Home --> Profile[ProfileFragment\nUser profile]
    Home --> Notifications[NotificationFragment\nNotifications]

    Dashboard --> DashboardTab[DashboardFragment\nCounts and actions]
    Dashboard --> New[NewFragment\nNew leads]
    Dashboard --> Interested[InterestedFragment\nInterested leads]
     Package root for all call-related code: `propfinsolutions.realstate.app.com.propfinsolutions`.
     Status-tab callers are implemented in `ui.fragments`: `NewFragment`, `InterestedFragment`, `MeetingDoneFragment`, `VisitDoneFragment`, and `BookingDoneFragment`.
     Calendar calling is implemented in `ui.fragments.calendar.CalendarFragment`.
     Common status-list call clicks originate in `lead_list.LeadsFilterAdapter` and are delegated through `RecyclerOnClick.onItemCallClick(...)`.
     Calendar call clicks originate in `calendar.EventAdapter` and are delegated through `EventCallOnClick.onItemCallClick(EventModel)`.
     Call buttons use Android `Intent.ACTION_CALL` with `tel:<contactNumber>`. There is no `ACTION_DIAL` implementation.
     The phone number is used only to construct the call Intent. It is not sent to `addcalllogs.php`.
     WhatsApp is a separate `ACTION_VIEW` flow using `http://api.whatsapp.com/send?phone=+91<contactNo>`; it does not explicitly target the WhatsApp package.
     The app does not answer incoming calls, record audio, or provide a replacement phone dialer.
    DashboardTab -->|Check in| Attendance[CheckInOutActivity\nCheck-in]
    DashboardTab -->|Check out| AttendanceOut[CheckInOutActivity\nCheck-out]
    DashboardTab -->|Follow-up card| LeadList[LeadListActivity\nLeadListFragment]

    New --> Detail[ViewDetailActivity\nViewDetailFragment]
    Interested --> Detail
    Meetings --> Detail
    Visits --> Detail
    Bookings --> Detail
    LeadList --> Detail
    Notifications -->|Display only; no row route| NotificationListEnd[Notification remains in list]

    Calendar -->|Select follow-up| Detail
    Calendar -->|Call lead| Phone[Android phone call]
    New -->|Call lead| Phone
    Interested -->|Call lead| Phone
    Meetings -->|Call lead| Phone
    Visits -->|Call lead| Phone
    Bookings -->|Call lead| Phone
    Phone -->|Call ends| AddCall[POST addcalllogs.php]
    AddCall --> CallLogs

    Detail -->|Follow-up| FollowUp[FollowUpActivity\nStatus and comment]
    FollowUp -->|Close lead| Home
    FollowUp -->|Other status| Detail

    Profile --> Settings[NotificationSoundSettingActivity\nNotification settings]
    Home -->|Logout| Login

    Firebase[MyFirebaseMessagingService\nPush receiver] --> PushNotification[Push notification]
    PushNotification -->|Tap push| PushDetail[ViewDetailActivityOne\nViewDetailFragment]
```

## 3. Screen Inventory

The app has 13 manifest-declared Activities. Several Activities host Fragments, so the user-facing page count is larger than the Activity count.

### 3.1 Startup and Authentication

  - Displays the startup screen for approximately three seconds.
  - Checks the stored login state.
  - Requests runtime permissions for notifications, SMS, camera, location, and storage where applicable.
  - Obtains and stores the Firebase/FCM token.
- Connections:
- Layout: `activity_login.xml`
- Work performed:
  - Accepts a ten-digit mobile number.
  - Calls `login.php` with the mobile number and push token.
  - Starts Firebase phone verification when the login response succeeds.
- Work performed:
  - Reads the phone number and Firebase verification ID from the Intent.
  - Verifies the entered OTP through Firebase.
  - Calls `login.php` again after Firebase verification.
  - Stores the authenticated state, user name, and login data.
     The initial request is `ApiInterface.getCallLogs(key)`.
     There is no page, offset, limit, or cursor parameter in the request.
     The current Fragment has no search implementation.
     `onResume()` does not reload the records.
### 3.2 Authenticated Home Shell

#### 4. Home container

- Class: `HomeActivity`
- Layout: `activity_home.xml`
- Work performed:
  - Toolbar bell -> `NotificationFragment`.
  - Logout -> clears the active session and returns to the authentication flow.

#### 5. Dashboard host

- Class: `HomeFragment`
- Layout: `fragment_home.xml`
  - Tab 1 -> `NewFragment`.
  - Tab 2 -> `InterestedFragment`.
  - Tab 3 -> `MeetingDoneFragment`.
  - Tab 4 -> `VisitDoneFragment`.
  - Tab 5 -> `BookingDoneFragment`.

#### 6. Dashboard tab

- Class: `DashboardFragment`
- Layout: `fragment_dashboard.xml`
- Work performed:
  - Shows lead totals, open/in-process/closed counts, daily activity, pending follow-ups, and check-in status.
  - Calls lead-count and attendance-status APIs.
- Connections:
  - Add lead action -> `NewLeadActivity`.
  - Check-in action -> `CheckInOutActivity` with `checkData=checkIn`.
  - Check-out action -> `CheckInOutActivity` with `checkData=checkOut`.
  - Summary cards -> `LeadListActivity` with the corresponding numeric lead type.
  - Follow-up card -> `LeadListActivity` with the follow-up lead type.

Dashboard cards are clickable through `DashboardAdapter` and `TodayListStatusAdapter`. Active mappings include total leads (`3`), open (`1`), in process (`4`), closed (`2`), today total (`6`), pending (`8`), today activity (`7`), and today closed (`9`). The backend remains authoritative for the exact meaning of these values.

### 3.3 Lead Status Tabs

All five status fragments load filtered leads through `Leads_Filter.php`. Each row can open lead details and can initiate an outgoing call.

#### 7. New leads

- Class/layout: `NewFragment` / `fragment_new.xml`
- Filter: `new`
- Connections: lead row -> `ViewDetailActivity`; call button -> Android phone call.

#### 8. Interested leads

- Class/layout: `InterestedFragment` / `fragment_interested.xml`
- Filter: `interested`
- Connections: lead row -> `ViewDetailActivity`; call button -> Android phone call.

#### 9. Completed meetings

- Class/layout: `MeetingDoneFragment` / `fragment_meeting_done.xml`
- Filter: `meeting`
- Connections: lead row -> `ViewDetailActivity`; call button -> Android phone call.

#### 10. Completed visits

- Class/layout: `VisitDoneFragment` / `fragment_visit_done.xml`
- Filter: `visit`
- Connections: lead row -> `ViewDetailActivity`; call button -> Android phone call.

#### 11. Booked leads

- Class/layout: `BookingDoneFragment` / `fragment_booking_done.xml`
- Filter: `booked`
- Connections: lead row -> `ViewDetailActivity`; call button -> Android phone call.

The implemented `FailedFragment` has the same list/detail/call pattern, but its pager entry is commented out, so it is not an active dashboard tab.

Each active status list also exposes the common actions implemented by `LeadsFilterAdapter`:

- Arrow/list action -> the owning fragment's list route, generally `LeadListActivity` with the selected lead type.
- Detail/calendar action -> `ViewDetailActivity`.
- Chat action -> external WhatsApp using an `ACTION_VIEW` URL.
- Phone action -> Android `ACTION_CALL`, followed by call-duration tracking.

### 3.4 Lead Operations

#### 12. New lead form

- Activity: `NewLeadActivity`
- Fragment: `NewLeadFragment`
- Layouts: `activity_new_lead.xml`, `fragment_new_lead.xml`
- Work performed:
  - Loads form master data through `master.php`.
  - Validates lead fields.
  - Creates a new lead through `newlead.php`.
- Connections:
  - Opened from `DashboardFragment`.
  - Successful submission -> `HomeActivity`.

#### 13. Lead list

- Activity: `LeadListActivity`
- Fragment: `LeadListFragment`
- Layouts: `activity_lead_list.xml`, `fragment_list_lead.xml`
- Work performed:
  - Reads the selected lead type from shared preferences.
  - Loads leads through `Leads_list.php`.
  - Displays the result and supports local text filtering.
- Connections:
  - Opened from dashboard count/follow-up actions.
  - Selecting a row -> `ViewDetailActivity`.
  - The list also supports local filtering and the common arrow, detail, WhatsApp, and phone actions where supplied by its adapter.

#### 14. Lead detail

- Activities: `ViewDetailActivity` and `ViewDetailActivityOne`
- Fragment: `ViewDetailFragment`
- Layout: `fragment_view_detail.xml` hosted by `activity_view_detail.xml`
- Work performed:
  - Loads lead details through `Leads_Detail.php`.
  - Loads previous follow-ups through `previous_followup.php`.
  - Displays lead information and follow-up history.
  - Hides follow-up controls for closed or specific final-status leads.
- Connections:
  - Normal list row -> `ViewDetailActivity`.
  - Push notification -> `ViewDetailActivityOne`.
  - Follow-up action -> `FollowUpActivity`.
  - The edit-detail action is currently commented out.

#### 15. Follow-up form

- Class: `FollowUpActivity`
- Layout: `activity_follow_up.xml`
- Work performed:
  - Accepts a status, date, time, and comment.
  - Maps the selected status to the backend status value.
  - Saves through `addnewfollowup.php`.
- Connections:
  - Opened from `ViewDetailFragment`.
  - Close lead -> `HomeActivity`.
  - Other follow-up statuses -> returns to the previous detail/list flow.

#### 16. Check-in/check-out

- Class: `CheckInOutActivity`
- Layout: `activity_check_in_out.xml`
- Work performed:
  - Gets the current device location.
  - Reverse-geocodes the location.
  - Sends `checkin` or `checkout` to `employee_checkin_checkout.php`.
  - Stores the successful check-in date for checkout.
- Connections:
  - Opened from `DashboardFragment`.
  - Check-in and check-out both return to the authenticated flow after completion.

### 3.5 Calendar and Call Logs

#### 17. Calendar/follow-ups

- Class: `CalendarFragment`
- Layout: `fragment_calendar.xml`
- Work performed:
  - Loads follow-ups through `userfollowups.php`.
  - Supports week/day selection and refreshes the selected date's events.
  - Shows follow-up dates and related leads.
  - Allows a lead call and observes Android call-state changes.
- Connections:
  - Event row -> displays the event; the active event adapter exposes a call action rather than a detail-navigation action.
  - Call action -> Android `ACTION_CALL`.
  - Call ending -> `addcalllogs.php`.

#### 18. Call logs

- Class: `CallLogsFragment`
- Layout: `fragment_call_logs.xml`
- Work performed:
  - Loads call records from the backend through `calllogs.php`.
  - Displays records using `CallLogsAdapter`.
  - Requests additional pages through the pagination helper.
- Important behavior:
  - This screen does not query the device's native call-history database.
  - It displays records previously submitted by this application/backend.

#### Call handling used by lead screens

- Call buttons use Android `Intent.ACTION_CALL` with `tel:<number>`.
- `CALL_PHONE` and phone-state permissions are required.
- The app listens for ringing, connected, and idle states while the relevant Fragment is active.
- When the call becomes idle, the app submits lead ID, start time, end time, and user key through `addcalllogs.php`.
- The app does not answer incoming calls, record audio, or provide a replacement phone dialer.

### 3.6 Profile, Notifications, and Push

#### 19. Profile

- Class: `ProfileFragment`
- Layout: `fragment_profile.xml`
- Work performed:
  - Displays stored user information.
- Connections:
  - Notification settings -> `NotificationSoundSettingActivity`.
  - Profile logout controls are commented out; the active logout is in `HomeActivity`.

#### 20. Notification sound settings

- Class: `NotificationSoundSettingActivity`
- Layout: `activity_notification_sound_setting.xml`
- Work performed: manages notification sound/volume preferences.
- Entry: `ProfileFragment`.

#### 21. In-app notifications

- Class: `NotificationFragment`
- Layout: `fragment_notification.xml`
- Work performed:
  - Loads notifications through `usernotifications.php`.
  - Supports pagination.
  - The Home toolbar marks notifications as read through `notification_read.php` when the bell is opened.
- Connections:
  - Notification selection -> `ViewDetailActivityOne` for the related lead.

#### 22. Push notification detail route

- Service: `MyFirebaseMessagingService`
- Detail Activity: `ViewDetailActivityOne`
- Work performed:
  - Receives Firebase push data.
  - Creates a notification channel and notification.
  - Creates a PendingIntent targeting the notification detail Activity.
- Current limitation:
  - The active push handler does not currently store the parsed lead ID, while older storage code is commented out. A push may therefore open detail with an invalid or zero lead ID unless another component has stored the expected preference.

#### Notification list row behavior

`NotificationAdapter` currently renders notification title, content, and relative time only. In-app notification rows are display-only and do not currently open lead details. Lead-detail navigation belongs to the Firebase push PendingIntent path.

### 3.7 Meeting Completion Dialog

The lead-list action can open a meeting-completion dialog through `CustomAdapter`.

- Layout: `dialog_layout_meeting.xml`
- Work performed:
  - Loads a senior/team member from `Senior.php`.
  - Loads lead statuses from `Lead_status.php`.
  - Loads meeting types from `Meeting_Type.php`.
  - Collects the meeting details shown by the dialog.
  - Submits the completed meeting through `addfollowupmeeting.php`.
- This is a dialog surface rather than a separate Activity, but it is a distinct workflow and should be counted in a complete functionality inventory.

### 3.8 Dialogs and Temporary Screens

The app contains these temporary UI surfaces that are not separate Activities:

- Logout confirmation and back-navigation confirmation in `HomeActivity`.
- Shared loading/progress dialog through `DialogClass`.
- Login and lead-edit `ProgressDialog` instances.
- Follow-up date selection through Android `DatePickerDialog`.
- Follow-up time selection through Android `TimePickerDialog`.
- Meeting-completion dialog using `dialog_layout_meeting.xml`.
- Legacy custom date dialog using `date_picker_dialog.xml`; no active caller was confirmed.
- `time_picker_dialog.xml` exists, but the active follow-up flow uses the Android time picker.

### 3.9 System and External Flows

#### SMS and OTP receiver

`SmsBroadcastReceiver` listens for OTP SMS messages, parses the message, and stores the extracted OTP in shared preferences for the authentication flow.

#### WhatsApp

Lead list adapters can open WhatsApp externally using an `ACTION_VIEW` URL. The user leaves the app and WhatsApp or another compatible handler must be installed.

#### Location and geocoding

`CheckInOutActivity` uses fused location services to obtain the current position and `Geocoder` to resolve a readable address before sending attendance data.

#### Permissions and device capabilities

The manifest and runtime code cover internet/network state, Firebase notifications, SMS, camera, phone calling and phone state, call-log access, fine/coarse location, storage, vibration, wake lock, and camera/telephony hardware.

`READ_CALL_LOG` is declared, but the current Call Logs page does not query Android's native call-log provider. It displays server records returned by `calllogs.php`.

## 4. API and Data Connections

| Screen/function | API or Android operation | Result |
|---|---|---|
| Login | `login.php` | Starts OTP flow or reports login failure |
| OTP | Firebase phone auth, then `login.php` | Stores authenticated user and opens Home |
| Dashboard counts | `LeadsCount.php` | Shows lead totals and statuses |
| Dashboard attendance | `check_checkin.php` | Determines check-in/check-out action |
| New lead | `master.php`, `newlead.php` | Loads fields and creates lead |
| Lead lists | `Leads_Filter.php`, `Leads_list.php` | Loads filtered or typed lead lists |
| Lead details | `Leads_Detail.php`, `previous_followup.php` | Shows lead and follow-up history |
| Follow-up | `addnewfollowup.php` | Saves lead status and follow-up data |
| Calendar | `userfollowups.php` | Loads scheduled follow-ups |
| Call tracking | `addcalllogs.php` | Saves outgoing call timing |
| Call log page | `calllogs.php` | Loads saved backend call records |
| Notifications | `usernotifications.php`, `notification_read.php` | Loads notifications and marks them read |
| Attendance | `employee_checkin_checkout.php` | Saves check-in or check-out location/time |
| Meeting dialog data | `Senior.php`, `Lead_status.php`, `Meeting_Type.php` | Loads meeting form selections |
| Meeting completion | `addfollowupmeeting.php` | Saves meeting completion details |
| Lead editing | `leadupdate.php` | Updates lead details when the edit screen is reached |

## 5. Current Active User Journeys

### New user login

```text
Splash
  -> Login
  -> Firebase OTP verification
  -> Application login confirmation
  -> Home
```

### Existing user login

```text
Splash
  -> Home
```

### Create a lead

```text
Home
  -> Dashboard tab
  -> New Lead
  -> Submit
  -> Home
```

### View and update a lead

```text
Home
  -> Dashboard status tab or Lead List
  -> Lead Details
  -> Follow-Up
  -> Save status
  -> Lead Details or Home when closed
```

### Make and record a call

```text
Lead list, dashboard tab, or Calendar
  -> Tap call
  -> Android phone call
  -> Call ends
  -> addcalllogs.php
  -> Call Logs screen loads the backend record
```

### Attendance

```text
Home
  -> Dashboard
  -> Check In
  -> Location/API confirmation
  -> Dashboard

Home
  -> Dashboard
  -> Check Out
  -> Location/API confirmation
  -> Dashboard
```

### Notification deep link

```text
Firebase push
  -> Android notification
  -> ViewDetailActivityOne
  -> ViewDetailFragment

Home notification bell
  -> NotificationFragment
  -> Display-only notification list
```

## 6. Declared, Legacy, or Unreachable Components

These components exist in the source or manifest but are not part of the confirmed active flow:

- `ViewEditDetails`: complete edit screen and API code exist, but the active navigation call is commented out.
- `FailedFragment`: implemented failed-lead list, but its dashboard pager entry is commented out.
- `NavSliderFragment`: older side-navigation implementation; current Home navigation uses bottom navigation.
- `PushNotificationActivity`: declared route that immediately redirects to `ViewDetailActivityOne`; likely legacy.
- `MainActivity`: source file exists but is not declared in the manifest and has no confirmed active caller.
- Old MSG91 OTP code: remains commented out; current flow uses Firebase phone authentication.
- Legacy custom date-picker classes under `date_time/date`: compiled implementation with no confirmed active caller.
- Commented foreground-service declaration and older notification implementation.
- Commented profile logout implementation and older dashboard implementations.

## 7. Resource and Component Inventory

Important navigation and screen resources include:

- `bottom_nav_menu.xml`: Home, Calendar, Call Logs, and Profile.
- `toolbar_menu.xml`: notification and logout actions.
- `menu_item_with_badage.xml`: toolbar notification badge.
- `custom_tab.xml`: dashboard pager tab appearance.
- `dialog_layout_meeting.xml`: meeting-completion dialog.
- `header_view.xml`: lead-detail header section.
- `fragment_nav_slider.xml`: legacy side navigation.
- `activity_main.xml`: layout for the undeclared legacy `MainActivity`.

Important reusable behavior components include:

- `CustomPageAdapter`: lead-list rows and detail navigation.
- `LeadsFilterAdapter`: filtered lead actions, WhatsApp, phone, detail, and list routes.
- `DashboardAdapter`: dashboard summary rows and routes.
- `TodayListStatusAdapter`: today-status dashboard rows and routes.
- `CallLogsAdapter`: backend call-log rows.
- `NotificationAdapter`: display-only notification rows.
- `EventAdapter`: calendar events and call action.

## 8. Navigation Notes and Risks

- `ViewDetailActivity` and `ViewDetailActivityOne` use the same `ViewDetailFragment`; they are separate entry points for normal list navigation and notification navigation.
- Lead IDs and lead types are passed partly through Intent extras and partly through shared preferences, so stale preference values can affect a screen if an Intent is missing data.
- The app requests `READ_CALL_LOG`, but the current Call Logs screen uses the backend API instead of Android's native call-log provider.
- Push detail navigation should be tested because the current Firebase service may not persist the lead ID before opening detail.
- The exact semantic meaning of some numeric dashboard lead types is defined by the backend; the local UI maps the values but does not contain the server's authoritative definitions.