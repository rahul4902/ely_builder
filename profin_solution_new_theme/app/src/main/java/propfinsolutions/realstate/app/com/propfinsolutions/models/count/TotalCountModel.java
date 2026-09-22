package propfinsolutions.realstate.app.com.propfinsolutions.models.count;


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class TotalCountModel {

    @SerializedName("today_count")
    @Expose
    private List<Today_count> today_count = null;

    public List<Today_count> getToday_count() {
        return today_count;
    }

    public void setToday_count(List<Today_count> readtoday_count) {
        this.today_count = readtoday_count;
    }



}


