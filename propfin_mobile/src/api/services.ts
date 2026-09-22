import apiClient, { toFormData } from './client';
import { Endpoints } from './endpoints';
import {
  LoginResponse,
  LeadCountResponse,
  TodayCountResponse,
  CheckInOutResponse,
  LeadFilterResponse,
  LeadListResponse,
  LeadDetailResponse,
  PreviousFollowupResponse,
  MasterResponse,
  CalendarEventsResponse,
  CallLogsResponse,
  UserNotificationsResponse,
  SeniorsResponse,
  StatusOptionResponse,
  MeetingTypeResponse,
} from '../types';

export const ApiService = {
  // Authentication
  async login(mobile: string, pushtoken: string = 'token_placeholder'): Promise<LoginResponse> {
    const res = await apiClient.post(
      Endpoints.LOGIN,
      toFormData({ mobile, pushtoken })
    );
    return res.data;
  },

  // Dashboard Counts
  async getLeadCount(key: string): Promise<LeadCountResponse> {
    const res = await apiClient.post(
      Endpoints.LEADS_COUNT,
      toFormData({ key })
    );
    return res.data;
  },

  async getLeadTotal(key: string): Promise<TodayCountResponse> {
    const res = await apiClient.post(
      Endpoints.LEADS_COUNT,
      toFormData({ key })
    );
    return res.data;
  },

  // Attendance
  async checkInStatus(key: string, date: string): Promise<CheckInOutResponse> {
    const res = await apiClient.post(
      Endpoints.CHECK_CHECKIN,
      toFormData({ key, _Date: date })
    );
    return res.data;
  },

  async employeeCheckIn(params: {
    key: string;
    lat: number;
    lng: number;
    check_in_rem: string;
    check_in_address: string;
    check_in_date: string;
  }): Promise<CheckInOutResponse> {
    const res = await apiClient.post(
      Endpoints.EMPLOYEE_CHECKIN_CHECKOUT,
      toFormData({
        key: params.key,
        lat: params.lat,
        lng: params.lng,
        type: 'checkin',
        check_in_rem: params.check_in_rem,
        check_in_address: params.check_in_address,
        check_in_date: params.check_in_date,
      })
    );
    return res.data;
  },

  async employeeCheckOut(params: {
    key: string;
    lat: number;
    lng: number;
    check_out_rem: string;
    check_out_address: string;
    check_in_date: string;
    check_out_date: string;
  }): Promise<CheckInOutResponse> {
    const res = await apiClient.post(
      Endpoints.EMPLOYEE_CHECKIN_CHECKOUT,
      toFormData({
        key: params.key,
        lat: params.lat,
        lng: params.lng,
        type: 'checkout',
        check_out_rem: params.check_out_rem,
        check_out_address: params.check_out_address,
        check_in_date: params.check_in_date,
        check_out_date: params.check_out_date,
      })
    );
    return res.data;
  },

  // Lead Filters (New, Interested, Meeting, Visit, Booking)
  async getLeadFilterList(key: string, status: string): Promise<LeadFilterResponse> {
    const res = await apiClient.post(
      Endpoints.LEADS_FILTER,
      toFormData({ key, status })
    );
    return res.data;
  },

  // Lead List by numeric status (1=Open, 2=Closed, 3=Total, 4=In Process, 6=Today Total, 7=Today Activity, 8=Pending, 9=Today Closed)
  async getLeadList(key: string, status: number): Promise<LeadListResponse> {
    const res = await apiClient.post(
      Endpoints.LEADS_LIST,
      toFormData({ key, status })
    );
    return res.data;
  },

  // Lead Detail
  async getDetailView(key: string, lead_id: number | string): Promise<LeadDetailResponse> {
    const res = await apiClient.post(
      Endpoints.LEADS_DETAIL,
      toFormData({ key, lead_id })
    );
    return res.data;
  },

  // Previous Followups
  async getPreviousFollowups(key: string, lead_id: number | string): Promise<PreviousFollowupResponse> {
    const res = await apiClient.post(
      Endpoints.PREVIOUS_FOLLOWUP,
      toFormData({ key, lead_id })
    );
    return res.data;
  },

  // Add Followup
  async addNewFollowup(params: {
    key: string;
    lead_id: string | number;
    follow_up_date: string;
    comment: string;
    status: string;
  }): Promise<any> {
    const res = await apiClient.post(
      Endpoints.ADD_NEW_FOLLOWUP,
      toFormData(params)
    );
    return res.data;
  },

  // Master Data
  async getLeadMaster(key: string): Promise<MasterResponse> {
    const res = await apiClient.post(
      Endpoints.MASTER,
      toFormData({ key })
    );
    return res.data;
  },

  // New Lead
  async createNewLead(params: {
    key: string;
    name: string;
    contact_no: string;
    email: string;
    country: string;
    state: string;
    city: string;
    Pincode: string;
    location: string;
    source: string;
    project: string;
    requrement: string;
    Budget: string;
    lead_type: string;
  }): Promise<any> {
    const res = await apiClient.post(
      Endpoints.NEW_LEAD,
      toFormData(params)
    );
    return res.data;
  },

  // Update Lead
  async updateLead(params: {
    key: string;
    lead_id: number | string;
    name: string;
    email: string;
    state: string;
    city: string;
    location: string;
    project: string;
    requirement: string;
    Budget: string;
    lead_type?: string;
    lead_typ?: string;
  }): Promise<any> {
    const payload = {
      ...params,
      lead_type: params.lead_type || params.lead_typ || 'Hot',
      lead_typ: params.lead_typ || params.lead_type || 'Hot',
    };
    const res = await apiClient.post(
      Endpoints.LEAD_UPDATE,
      toFormData(payload)
    );
    return res.data;
  },

  // Calendar
  async getCalendarFollowups(key: string): Promise<CalendarEventsResponse> {
    const res = await apiClient.post(
      Endpoints.USER_FOLLOWUPS,
      toFormData({ key })
    );
    return res.data;
  },

  // Call Logs
  async addCallLog(params: {
    key: string;
    lead_id: string | number;
    call_start_datetime: string;
    call_end_datetime: string;
  }): Promise<any> {
    const res = await apiClient.post(
      Endpoints.ADD_CALL_LOGS,
      toFormData(params)
    );
    return res.data;
  },

  async getCallLogs(key: string): Promise<CallLogsResponse> {
    const res = await apiClient.post(
      Endpoints.CALL_LOGS,
      toFormData({ key })
    );
    return res.data;
  },

  // Notifications
  async getUserNotifications(key: string): Promise<UserNotificationsResponse> {
    const res = await apiClient.post(
      Endpoints.USER_NOTIFICATIONS,
      toFormData({ key })
    );
    return res.data;
  },

  async markNotificationRead(key: string): Promise<any> {
    const res = await apiClient.post(
      Endpoints.NOTIFICATION_READ,
      toFormData({ key })
    );
    return res.data;
  },

  // Meeting Dialog Data
  async getSeniors(key: string, status: number = 1): Promise<SeniorsResponse> {
    const res = await apiClient.post(
      Endpoints.SENIOR,
      toFormData({ key, status })
    );
    return res.data;
  },

  async getLeadStatuses(key: string, status: number = 1): Promise<StatusOptionResponse> {
    const res = await apiClient.post(
      Endpoints.LEAD_STATUS,
      toFormData({ key, status })
    );
    return res.data;
  },

  async getMeetingTypes(key: string, status: number = 1): Promise<MeetingTypeResponse> {
    const res = await apiClient.post(
      Endpoints.MEETING_TYPE,
      toFormData({ key, status })
    );
    return res.data;
  },

  async addFollowupMeeting(params: {
    key: string;
    follow_id: string | number;
    comment: string;
    senior_visit: string;
    meeting_type: string;
    follow_up_status: string;
  }): Promise<any> {
    const res = await apiClient.post(
      Endpoints.ADD_FOLLOWUP_MEETING,
      toFormData(params)
    );
    return res.data;
  },
};
