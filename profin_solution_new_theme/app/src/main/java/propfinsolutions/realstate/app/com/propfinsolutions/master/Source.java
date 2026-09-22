package propfinsolutions.realstate.app.com.propfinsolutions.master;

/**
 * Created by Divakar on 7/15/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Source {

    @SerializedName("source_name")
    @Expose
    private String source_name;

    public String getSource_name() {
        return source_name;
    }
}