package propfinsolutions.realstate.app.com.propfinsolutions.models.calendar;
import java.util.List;

import com.google.gson.annotations.SerializedName;
public class CalendarUserFollowUpRes {

    @SerializedName("Userfollowups")
    List<Userfollowups> Userfollowups;

    public void setUserfollowups(List<Userfollowups> Userfollowups) {
        this.Userfollowups = Userfollowups;
    }
    public List<Userfollowups> getUserfollowups() {
        return Userfollowups;
    }

    public class Userfollowups {

        @SerializedName("id")
        String id;

        @SerializedName("title")
        String title;

        @SerializedName("note")
        String note;

        @SerializedName("name")
        String name;

        @SerializedName("contact_no")
        String contactNo;

        @SerializedName("email")
        String email;

        @SerializedName("source")
        String source;

        @SerializedName("country")
        String country;

        @SerializedName("state")
        String state;

        @SerializedName("city")
        String city;

        @SerializedName("pin")
        String pin;

        @SerializedName("location")
        String location;

        @SerializedName("project")
        String project;

        @SerializedName("requirement")
        String requirement;

        @SerializedName("Budget")
        String Budget;

        @SerializedName("status")
        String status;

        @SerializedName("user_assigned_id")
        String userAssignedId;

        @SerializedName("client_id")
        String clientId;

        @SerializedName("user_created_id")
        String userCreatedId;

        @SerializedName("contact_date")
        String contactDate;

        @SerializedName("created_at")
        String createdAt;

        @SerializedName("updated_at")
        String updatedAt;

        @SerializedName("lead_type")
        String leadType;

        @SerializedName("action_date")
        String actionDate;

        @SerializedName("lead_id")
        String leadId;

        @SerializedName("user_id")
        String userId;

        @SerializedName("follow_up_date")
        String followUpDate;

        @SerializedName("comment")
        String comment;

        @SerializedName("meeting_date")
        String meetingDate;

        @SerializedName("senior_visit")
        String seniorVisit;

        @SerializedName("meeting_type")
        String meetingType;

        @SerializedName("follow_up_status")
        String followUpStatus;


        public void setId(String id) {
            this.id = id;
        }
        public String getId() {
            return id;
        }

        public void setTitle(String title) {
            this.title = title;
        }
        public String getTitle() {
            return title;
        }

        public void setNote(String note) {
            this.note = note;
        }
        public String getNote() {
            return note;
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

        public void setEmail(String email) {
            this.email = email;
        }
        public String getEmail() {
            return email;
        }

        public void setSource(String source) {
            this.source = source;
        }
        public String getSource() {
            return source;
        }

        public void setCountry(String country) {
            this.country = country;
        }
        public String getCountry() {
            return country;
        }

        public void setState(String state) {
            this.state = state;
        }
        public String getState() {
            return state;
        }

        public void setCity(String city) {
            this.city = city;
        }
        public String getCity() {
            return city;
        }

        public void setPin(String pin) {
            this.pin = pin;
        }
        public String getPin() {
            return pin;
        }

        public void setLocation(String location) {
            this.location = location;
        }
        public String getLocation() {
            return location;
        }

        public void setProject(String project) {
            this.project = project;
        }
        public String getProject() {
            return project;
        }

        public void setRequirement(String requirement) {
            this.requirement = requirement;
        }
        public String getRequirement() {
            return requirement;
        }

        public void setBudget(String Budget) {
            this.Budget = Budget;
        }
        public String getBudget() {
            return Budget;
        }

        public void setStatus(String status) {
            this.status = status;
        }
        public String getStatus() {
            return status;
        }

        public void setUserAssignedId(String userAssignedId) {
            this.userAssignedId = userAssignedId;
        }
        public String getUserAssignedId() {
            return userAssignedId;
        }

        public void setClientId(String clientId) {
            this.clientId = clientId;
        }
        public String getClientId() {
            return clientId;
        }

        public void setUserCreatedId(String userCreatedId) {
            this.userCreatedId = userCreatedId;
        }
        public String getUserCreatedId() {
            return userCreatedId;
        }

        public void setContactDate(String contactDate) {
            this.contactDate = contactDate;
        }
        public String getContactDate() {
            return contactDate;
        }

        public void setCreatedAt(String createdAt) {
            this.createdAt = createdAt;
        }
        public String getCreatedAt() {
            return createdAt;
        }

        public void setUpdatedAt(String updatedAt) {
            this.updatedAt = updatedAt;
        }
        public String getUpdatedAt() {
            return updatedAt;
        }

        public void setLeadType(String leadType) {
            this.leadType = leadType;
        }
        public String getLeadType() {
            return leadType;
        }

        public void setActionDate(String actionDate) {
            this.actionDate = actionDate;
        }
        public String getActionDate() {
            return actionDate;
        }

        public void setLeadId(String leadId) {
            this.leadId = leadId;
        }
        public String getLeadId() {
            return leadId;
        }

        public void setUserId(String userId) {
            this.userId = userId;
        }
        public String getUserId() {
            return userId;
        }

        public void setFollowUpDate(String followUpDate) {
            this.followUpDate = followUpDate;
        }
        public String getFollowUpDate() {
            return followUpDate;
        }

        public void setComment(String comment) {
            this.comment = comment;
        }
        public String getComment() {
            return comment;
        }

        public void setMeetingDate(String meetingDate) {
            this.meetingDate = meetingDate;
        }
        public String getMeetingDate() {
            return meetingDate;
        }

        public void setSeniorVisit(String seniorVisit) {
            this.seniorVisit = seniorVisit;
        }
        public String getSeniorVisit() {
            return seniorVisit;
        }

        public void setMeetingType(String meetingType) {
            this.meetingType = meetingType;
        }
        public String getMeetingType() {
            return meetingType;
        }

        public void setFollowUpStatus(String followUpStatus) {
            this.followUpStatus = followUpStatus;
        }
        public String getFollowUpStatus() {
            return followUpStatus;
        }

    }
}
