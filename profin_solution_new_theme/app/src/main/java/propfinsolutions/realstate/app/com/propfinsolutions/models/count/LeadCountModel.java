package propfinsolutions.realstate.app.com.propfinsolutions.models.count;


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class LeadCountModel {

    @SerializedName("record_count")
    @Expose
    private List<RecordCount> recordCount = null;

    public List<RecordCount> getRecordCount() {
        return recordCount;
    }

    public void setRecordCount(List<RecordCount> recordCount) {
        this.recordCount = recordCount;
    }




}


