package propfinsolutions.realstate.app.com.propfinsolutions.models.count;

/**
 * Created by Divakar on 7/7/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class RecordCount {

    @SerializedName("open")
    @Expose
    private Integer open;
    @SerializedName("close")
    @Expose
    private Integer close;
    @SerializedName("total")
    @Expose
    private Integer total;
    @SerializedName("Progress")
    @Expose
    private Integer progress;

    public Integer getOpen() {
        return open;
    }

    public void setOpen(Integer open) {
        this.open = open;
    }

    public Integer getClose() {
        return close;
    }

    public void setClose(Integer close) {
        this.close = close;
    }

    public Integer getTotal() {
        return total;
    }

    public void setTotal(Integer total) {
        this.total = total;
    }

    public Integer getProgress() {
        return progress;
    }

    public void setProgress(Integer progress) {
        this.progress = progress;
    }

}