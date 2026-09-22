package propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList;


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class LeadListModel {

    @SerializedName("Lead_List")
    @Expose
    private List<LeadList> leadList = null;

    public List<LeadList> getLeadList() {
        return leadList;
    }

    public void setLeadList(List<LeadList> leadList) {
        this.leadList = leadList;
    }

}

