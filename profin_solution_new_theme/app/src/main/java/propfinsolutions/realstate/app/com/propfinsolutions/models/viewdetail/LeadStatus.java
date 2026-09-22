package propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class LeadStatus {

    @SerializedName("Lead_Status")
    public List<Lead_lsit> data;
    public  class Lead_lsit{
        @SerializedName("id")
        public String id;
        @SerializedName("status")
        public String status;

    }
}
