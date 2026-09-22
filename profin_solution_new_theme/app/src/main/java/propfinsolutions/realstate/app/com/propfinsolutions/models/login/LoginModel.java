package propfinsolutions.realstate.app.com.propfinsolutions.models.login;

/**
 * Created by Divakar on 7/6/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class LoginModel {

    @SerializedName("record")
    @Expose
    private List<Record> record = null;

    public List<Record> getRecord() {
        return record;
    }

    public void setRecord(List<Record> record) {
        this.record = record;
    }

}

