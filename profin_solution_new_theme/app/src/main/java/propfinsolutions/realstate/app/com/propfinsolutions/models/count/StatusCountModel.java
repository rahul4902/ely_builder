package propfinsolutions.realstate.app.com.propfinsolutions.models.count;


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class StatusCountModel {

    @SerializedName("status_for_list")
    @Expose
    private List<Status_for_list> status_for_list = null;

    public List<Status_for_list> getStatus_for_list() {
        return status_for_list;
    }

    public void setStatus_for_list(List<Status_for_list> readstatus_for_list) {
        this.status_for_list = readstatus_for_list;
    }

}


