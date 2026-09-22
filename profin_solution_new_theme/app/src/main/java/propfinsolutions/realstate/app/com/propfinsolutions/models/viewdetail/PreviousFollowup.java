package propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail;

/**
 * Created by Divakar on 7/13/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class PreviousFollowup {

    @SerializedName("follow_up_date")
    @Expose
    private String followUpDate;
    @SerializedName("comment")
    @Expose
    private String comment;

    @SerializedName("msg")
    @Expose
    private String msg;

    @SerializedName("meeting_date")
    @Expose
    private String meeting_date;

    @SerializedName("id")
    @Expose
    private String id;

    public String getId() {
        return id;
    }

    public PreviousFollowup(String followUpDate, String comment, String meeting_date) {
        this.followUpDate = followUpDate;
        this.comment = comment;
        this.meeting_date=meeting_date;
    }

    public String getMeeting_date() {
        return meeting_date;
    }

    public void setMeeting_date(String meeting_date) {
        this.meeting_date = meeting_date;
    }

    public String getMsg() {
        return msg;
    }

    public String getFollowUpDate() {
        return followUpDate;
    }

    public void setFollowUpDate(String followUpDate) {
        this.followUpDate = followUpDate;
    }

    public String getComment() {
        return comment;
    }

    public void setComment(String comment) {
        this.comment = comment;
    }

}