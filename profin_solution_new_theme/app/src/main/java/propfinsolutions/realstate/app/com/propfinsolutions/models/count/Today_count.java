package propfinsolutions.realstate.app.com.propfinsolutions.models.count;

/**
 * Created by Divakar on 7/7/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Today_count {
    @SerializedName("TodayTotal")
    @Expose
    private Integer TodayTotal;
    @SerializedName("TodayOpen")
    @Expose
    private Integer TodayOpen;

    @SerializedName("TodayClose")
    @Expose
    private Integer TodayClose;


    @SerializedName("TodayPending")
    @Expose
    private Integer TodayPending;

    public Integer getTodayTotal() {
        return TodayTotal;
    }

    public void setTodayTotal(Integer todayTotal) {
        TodayTotal = todayTotal;
    }

    public Integer getTodayOpen() {
        return TodayOpen;
    }

    public void setTodayOpen(Integer todayOpen) {
        TodayOpen = todayOpen;
    }

    public Integer getTodayClose() {
        return TodayClose;
    }

    public void setTodayClose(Integer todayClose) {
        TodayClose = todayClose;
    }

    public Integer getTodayPending() {
        return TodayPending;
    }

    public void setTodayPending(Integer todayPending) {
        TodayPending = todayPending;
    }

}