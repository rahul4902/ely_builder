// Authentication
export interface LoginRecord {
  status: number;
  msg: string;
  key?: string;
  Name?: string;
}

export interface LoginResponse {
  record: LoginRecord[];
}

// Dashboard Counts
export interface LeadCountRecord {
  total?: number;
  open?: number;
  progress?: number;
  Progress?: number;
  close?: number;
}

export interface LeadCountResponse {
  record_count?: LeadCountRecord[];
  recordCount?: LeadCountRecord[];
  today_count?: TodayCountRecord[];
}

export interface TodayCountRecord {
  TodayTotal?: number;
  TodayOpen?: number;
  TodayPending?: number;
  TodayClose?: number;
  today_total?: number;
  today_open?: number;
  today_pending?: number;
  today_close?: number;
}

export interface TodayCountResponse {
  today_count: TodayCountRecord[];
}

// Lead Filter (New, Interested, Meeting, Visit, Booking)
export interface LeadFilterItem {
  id: string;
  title?: string;
  note?: string;
  name: string;
  contact_no: string;
  email?: string;
  source?: string;
  country?: string;
  state?: string;
  city?: string;
  pin?: string;
  location?: string;
  project?: string;
  requirement?: string;
  Budget?: string;
  status?: string;
  user_assigned_id?: string;
  client_id?: string;
  user_created_id?: string;
  contact_date?: string;
  created_at?: string;
  updated_at?: string;
  lead_type?: string;
  action_date?: string;
  budget?: string;
  user_name?: string;
  nextfollowup?: string;
  meetingdate?: string;
}

export interface LeadFilterResponse {
  Lead_List: LeadFilterItem[];
}

// Lead List
export interface LeadListItem {
  id: string;
  title?: string;
  note?: string;
  name: string;
  contact_no: string;
  email?: string;
  source?: string;
  country?: string;
  state?: string;
  city?: string;
  pin?: string;
  location?: string;
  project?: string;
  requirement?: string;
  Budget?: string;
  status?: string;
  user_assigned_id?: string;
  client_id?: string;
  user_created_id?: string;
  contact_date?: string;
  created_at?: string;
  updated_at?: string;
  lead_type?: string;
  action_date?: string;
  budget?: string;
  user_name?: string;
  nextfollowup?: string;
  meetingdate?: string;
}

export interface LeadListResponse {
  Leads_list?: LeadListItem[];
  Lead_List?: LeadListItem[];
  record?: LeadListItem[];
  message?: Array<{ msg: string; status: number }>;
}

// Lead Detail
export interface LeadDetailRecord {
  id: string;
  name: string;
  contact_no: string;
  email: string;
  source: string;
  location: string;
  project: string;
  Budget: string;
  requirement: string;
  country: string;
  state: string;
  city: string;
  pin: string;
  status: string;
  lead_type?: string;
  created_at?: string;
  next_follow_up_date?: string;
  follow_up_date?: string;
}

export interface LeadDetailResponse {
  record?: LeadDetailRecord[];
  DetailRecord?: LeadDetailRecord[];
  detail?: LeadDetailRecord;
  message?: Array<{ msg: string; status: number }>;
}

// Previous Followup
export interface PreviousFollowupItem {
  id: string;
  lead_id: string;
  user_id: string;
  follow_up_date: string;
  comment: string;
  status: string;
  created_at: string;
  user_name?: string;
  meeting_date?: string;
}

export interface PreviousFollowupResponse {
  followup?: PreviousFollowupItem[];
  PreviousFollowup?: PreviousFollowupItem[];
  message?: Array<{ msg: string; status: number }>;
}

// Master Dropdown Data
export interface MasterItem {
  id?: string;
  name?: string;
  project_name?: string;
  requirement_name?: string;
  source_name?: string;
  budget_range?: string;
}

export interface MasterResponse {
  project: MasterItem[];
  requirement: MasterItem[];
  source: MasterItem[];
  budget: MasterItem[];
}

// Attendance / Check In & Out
export interface EmpCheckInData {
  id: string;
  user_id: string;
  check_in_date: string;
  check_in_lat: string;
  check_in_lng: string;
  check_in_rem: string;
  check_in_address: string;
  check_out_lat: string;
  check_out_lng: string;
  check_out_address: string;
  check_out_rem: string;
  check_out_date: string | null;
}

export interface CheckInOutResponse {
  status: string;
  data: {
    msg?: string;
    key?: string;
    empcheckindata: EmpCheckInData[];
  };
}

// Calendar & Followups
export interface CalendarEventItem {
  id: string;
  name: string;
  contact_no: string;
  follow_up_date: string;
  comment: string;
  status: string;
  lead_id: string;
  project?: string;
  location?: string;
}

export interface CalendarEventsResponse {
  followup?: CalendarEventItem[];
  Userfollowups?: CalendarEventItem[];
  message?: Array<{ msg: string; status: number }>;
}

// Call Logs
export interface CallLogItem {
  id: string;
  call_start_datetime: string;
  call_end_datetime: string;
  user_id: string;
  created_at: string;
  lead_id: string;
  name: string;
  contact_no: string;
}

export interface CallLogsResponse {
  calllogs: CallLogItem[];
}

// Notifications
export interface UserNotificationItem {
  id: string;
  title: string;
  message: string;
  created_at: string;
  is_read?: string | number;
  lead_id?: string;
}

export interface UserNotificationsResponse {
  UserNotifications: UserNotificationItem[];
}

// Meeting Dialog Data
export interface SeniorItem {
  id: string;
  name: string;
}

export interface SeniorsResponse {
  record: SeniorItem[];
}

export interface StatusOptionItem {
  id: string;
  name: string;
}

export interface StatusOptionResponse {
  record: StatusOptionItem[];
}

export interface MeetingTypeItem {
  id: string;
  name: string;
}

export interface MeetingTypeResponse {
  record: MeetingTypeItem[];
}

// Navigation Types
export type RootStackParamList = {
  Splash: undefined;
  Login: undefined;
  OtpVerification: { mobile: string };
  MainApp: undefined;
  NewLead: undefined;
  LeadList: { leadType: number; title: string };
  LeadDetail: { leadId: string | number; status?: string };
  FollowUp: { leadId: string | number };
  CheckInOut: { type: 'checkIn' | 'checkOut' };
  Notifications: undefined;
  NotificationSoundSetting: undefined;
};

export type BottomTabParamList = {
  HomeTab: undefined;
  CalendarTab: undefined;
  CallLogsTab: undefined;
  ProfileTab: undefined;
};

export type HomeTopTabParamList = {
  Dashboard: undefined;
  NewLeads: undefined;
  InterestedLeads: undefined;
  MeetingDoneLeads: undefined;
  VisitDoneLeads: undefined;
  BookingDoneLeads: undefined;
};
