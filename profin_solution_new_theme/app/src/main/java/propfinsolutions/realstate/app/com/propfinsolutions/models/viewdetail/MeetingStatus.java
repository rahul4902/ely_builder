package propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class MeetingStatus {

    @SerializedName("Meeting_type")
    public List<Lead_lsit> data;
    public  class Lead_lsit{
        @SerializedName("id")
        public String id;
        @SerializedName("type")
        public String type;

    }
}
