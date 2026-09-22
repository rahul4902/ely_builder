package propfinsolutions.realstate.app.com.propfinsolutions.models.count;

/**
 * Created by Divakar on 7/7/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Status_for_list {

    @SerializedName("TotalOpen")
    @Expose
    private Integer TotalOpen;
    @SerializedName("TotalClose")
    @Expose
    private Integer TotalClose;
    @SerializedName("TotalLeads")
    @Expose
    private Integer TotalLeads;
    @SerializedName("TotalPending")
    @Expose
    private Integer TotalPending;
    @SerializedName("TotalDumpLead")
    @Expose
    private Integer TotalDumpLead;
    @SerializedName("TodayTotal")
    @Expose
    private Integer TodayTotal;

    @SerializedName("TodayTotal")
    @Expose
    private Integer TodayOpen;


    @SerializedName("TodayPending")
    @Expose
    private Integer TodayPending;

    @SerializedName("TodayClose")
    @Expose
    private Integer TodayClose;


    public Integer getTotalOpen() {
        return TotalOpen;
    }

    public void setTotalOpen(Integer totalOpen) {
        TotalOpen = totalOpen;
    }

    public Integer getTotalClose() {
        return TotalClose;
    }

    public void setTotalClose(Integer totalClose) {
        TotalClose = totalClose;
    }

    public Integer getTotalLeads() {
        return TotalLeads;
    }

    public void setTotalLeads(Integer totalLeads) {
        TotalLeads = totalLeads;
    }

    public Integer getTotalPending() {
        return TotalPending;
    }

    public void setTotalPending(Integer totalPending) {
        TotalPending = totalPending;
    }

    public Integer getTotalDumpLead() {
        return TotalDumpLead;
    }

    public void setTotalDumpLead(Integer totalDumpLead) {
        TotalDumpLead = totalDumpLead;
    }

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

    public Integer getTodayPending() {
        return TodayPending;
    }

    public void setTodayPending(Integer todayPending) {
        TodayPending = todayPending;
    }

    public Integer getTodayClose() {
        return TodayClose;
    }

    public void setTodayClose(Integer todayClose) {
        TodayClose = todayClose;
    }






}