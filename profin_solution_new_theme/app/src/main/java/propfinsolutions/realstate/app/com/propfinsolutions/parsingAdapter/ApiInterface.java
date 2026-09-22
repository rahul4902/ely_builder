package propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter;

import propfinsolutions.realstate.app.com.propfinsolutions.master.MasterLeadModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calendar.CalendarUserFollowUpRes;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calllog.AddCallLogs;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calllog.GetCallLogsModelResponse;
import propfinsolutions.realstate.app.com.propfinsolutions.models.checkInOut.CheckInOutModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.LeadCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.TotalCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.LoginModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.newfollowup.NewFollowupModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.newlead.NewLeadModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationRead;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.LeadStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.MeetingStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.PrevFollowupModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.Seniors;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.ViewDetailModel;
import retrofit2.Call;
import retrofit2.http.Field;
import retrofit2.http.FormUrlEncoded;
import retrofit2.http.POST;

/**
 * Created by Divakar on 7/6/2017.
 */

public interface ApiInterface {
    String api = "API";

    @FormUrlEncoded
    @POST(api + "/login.php")
    Call<LoginModel> getLoginResponse(@Field("mobile") String amount, @Field("pushtoken") String pushtoken);

    @FormUrlEncoded
    @POST(api + "/LeadsCount.php")
    Call<LeadCountModel> getLeadCount(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/LeadsCount.php")
    Call<TotalCountModel> getLeadTotal(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/userfollowups.php")
    Call<CalendarUserFollowUpRes> getCalendarFollowUp(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/usernotifications.php")
    Call<NotificationResponseModel> getUserNotification(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/notification_read.php")
    Call<NotificationRead> getNotificationRead(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/Leads_Filter.php")
    Call<LeadsFilter> getLeadFilterList(@Field("key") String key1, @Field("status") String status);

    @FormUrlEncoded
    @POST(api + "/addcalllogs.php")
    Call<AddCallLogs> addCallLogs(@Field("call_start_datetime") String call_start_datetime,
                                  @Field("call_end_datetime") String call_end_datetime,
                                  @Field("lead_id") String lead_id,
                                  @Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/calllogs.php")
    Call<GetCallLogsModelResponse> getCallLogs(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/Leads_list.php")
    Call<LeadListModel> getLeadList(@Field("key") String key1, @Field("status") int status);

    @FormUrlEncoded
    @POST(api + "/Leads_Detail.php")
    Call<ViewDetailModel> getDetailView(@Field("key") String key1, @Field("lead_id") int status);

    @FormUrlEncoded
    @POST(api + "/previous_followup.php")
    Call<PrevFollowupModel> getDetailPreviousLead(@Field("key") String key1, @Field("lead_id") int status);

    @FormUrlEncoded
    @POST(api + "/addnewfollowup.php")
    Call<NewFollowupModel> getNewFollowUp(@Field("key") String key1, @Field("lead_id") String leadStatus, @Field("follow_up_date") String followupDate, @Field("comment") String comment, @Field("status") String status);

    @FormUrlEncoded
    @POST(api + "/master.php")
    Call<MasterLeadModel> getLeadMaster(@Field("key") String key1);

    @FormUrlEncoded
    @POST(api + "/newlead.php")
    Call<NewLeadModel> getNewLead(@Field("key") String key1, @Field("name") String name, @Field("contact_no") String contact_no, @Field("email") String email, @Field("country") String country, @Field("state") String state, @Field("city") String city, @Field("Pincode") String Pincode, @Field("location") String location, @Field("source") String source, @Field("project") String project, @Field("requrement") String requrement, @Field("Budget") String Budget, @Field("lead_type") String lead_type);

//    @FormUrlEncoded
//    @POST(api + "/addfollowupmeeting.php")
//    Call<MasterLeadModel> meeting(@Field("key") String key, @Field("follow_id") String follow_id);

    @FormUrlEncoded
    @POST(api + "/addfollowupmeeting.php")
    Call<MasterLeadModel> meeting(@Field("key") String key,
                                  @Field("comment") String comment,
                                  @Field("senior_visit") String senior_visit,
                                  @Field("meeting_type") String meeting_type,
                                  @Field("follow_up_status") String follow_up_status,
                                  @Field("follow_id") String follow_id);

    @FormUrlEncoded
    @POST(api + "/Senior.php")
    Call<Seniors> seniors_list(@Field("key") String key, @Field("status") int status);

    @FormUrlEncoded
    @POST(api + "/Lead_status.php")
    Call<LeadStatus> lead_status_response(@Field("key") String key, @Field("status") int status);

    @FormUrlEncoded
    @POST(api + "/Meeting_Type.php")
    Call<MeetingStatus> meeting_type_status(@Field("key") String key, @Field("status") int status);

    @FormUrlEncoded
    @POST(api + "/leadupdate.php")
    Call<LoginModel> leadUpdate(
            @Field("key") String key,
            @Field("lead_id") int lead_id,
            @Field("name") String name,
            @Field("email") String email,
            @Field("state") String state,
            @Field("city") String city,
            @Field("location") String location,
            @Field("project") String project,
            @Field("requirement") String requirement,
            @Field("Budget") String Budget,
            @Field("lead_typ") String lead_typ);

    @FormUrlEncoded
    @POST(api + "/employee_checkin_checkout.php")
    Call<CheckInOutModel> employee_checkin(@Field("key") String ApiToken,
                                           @Field("lat") double punch_in_lat,
                                           @Field("lng") double punch_in_lng,
                                           @Field("type") String checkin,
                                           @Field("check_in_rem") String check_in_rem,
                                           @Field("check_in_address") String check_in_address,
                                           @Field("check_in_date") String check_in_date);


    @FormUrlEncoded
    @POST(api + "/employee_checkin_checkout.php")
    Call<CheckInOutModel> employee_checkout(@Field("key") String ApiToken,
                                            @Field("lat") double punch_in_lat,
                                            @Field("lng") double punch_in_lng,
                                            @Field("type") String checkout,
                                            @Field("check_out_rem") String check_out_rem,
                                            @Field("check_out_address") String check_out_address,
                                            @Field("check_in_date") String actual_date,
                                            @Field("check_out_date") String check_out_date);

    @FormUrlEncoded
    @POST(api + "/check_checkin.php")
    Call<CheckInOutModel> check_in_status(@Field("key") String key, @Field("_Date") String _Date);

}