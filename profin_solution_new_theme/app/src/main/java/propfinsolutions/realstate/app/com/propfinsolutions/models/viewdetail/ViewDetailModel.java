package propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail;

/**
 * Created by Divakar on 7/12/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class ViewDetailModel {

    @SerializedName("DetailRecord")
    @Expose
    private List<DetailRecord> detailRecord = null;

    public List<DetailRecord> getDetailRecord() {
        return detailRecord;
    }

    public void setDetailRecord(List<DetailRecord> detailRecord) {
        this.detailRecord = detailRecord;
    }

}