package propfinsolutions.realstate.app.com.propfinsolutions.models.newlead;

/**
 * Created by Divakar on 7/14/2017.
 */


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class NewLeadModel {

    @SerializedName("new_lead")
    @Expose
    private List<NewLead> newLead = null;

    public List<NewLead> getNewLead() {
        return newLead;
    }

    public void setNewLead(List<NewLead> newLead) {
        this.newLead = newLead;
    }

}