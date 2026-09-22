package propfinsolutions.realstate.app.com.propfinsolutions.master;

/**
 * Created by Divakar on 7/15/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Budget {

    @SerializedName("budget_range")
    @Expose
    private String budget_range;

    public String getBudget_range() {
        return budget_range;
    }
}