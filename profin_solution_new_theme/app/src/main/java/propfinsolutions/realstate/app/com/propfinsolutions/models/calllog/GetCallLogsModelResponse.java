package propfinsolutions.realstate.app.com.propfinsolutions.models.calllog;
import java.util.List;

import com.google.gson.annotations.SerializedName;
public class GetCallLogsModelResponse {
    @SerializedName("calllogs")
    List<Calllogs> calllogs;


    public void setCalllogs(List<Calllogs> calllogs) {
        this.calllogs = calllogs;
    }
    public List<Calllogs> getCalllogs() {
        return calllogs;
    }

    public class Calllogs {

        @SerializedName("id")
        String id;

        @SerializedName("call_start_datetime")
        String callStartDatetime;

        @SerializedName("call_end_datetime")
        String callEndDatetime;

        @SerializedName("user_id")
        String userId;

        @SerializedName("created_at")
        String createdAt;

        @SerializedName("lead_id")
        String leadId;

        @SerializedName("name")
        String name;

        @SerializedName("contact_no")
        String contactNo;


        public void setId(String id) {
            this.id = id;
        }
        public String getId() {
            return id;
        }

        public void setCallStartDatetime(String callStartDatetime) {
            this.callStartDatetime = callStartDatetime;
        }
        public String getCallStartDatetime() {
            return callStartDatetime;
        }

        public void setCallEndDatetime(String callEndDatetime) {
            this.callEndDatetime = callEndDatetime;
        }
        public String getCallEndDatetime() {
            return callEndDatetime;
        }

        public void setUserId(String userId) {
            this.userId = userId;
        }
        public String getUserId() {
            return userId;
        }

        public void setCreatedAt(String createdAt) {
            this.createdAt = createdAt;
        }
        public String getCreatedAt() {
            return createdAt;
        }

        public void setLeadId(String leadId) {
            this.leadId = leadId;
        }
        public String getLeadId() {
            return leadId;
        }

        public void setName(String name) {
            this.name = name;
        }
        public String getName() {
            return name;
        }

        public void setContactNo(String contactNo) {
            this.contactNo = contactNo;
        }
        public String getContactNo() {
            return contactNo;
        }

    }
}
