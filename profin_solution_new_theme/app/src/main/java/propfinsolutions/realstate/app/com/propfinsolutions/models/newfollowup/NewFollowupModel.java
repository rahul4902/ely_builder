package propfinsolutions.realstate.app.com.propfinsolutions.models.newfollowup;

/**
 * Created by Divakar on 7/14/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class NewFollowupModel {

    @SerializedName("new_followup")
    @Expose
    private List<NewFollowup> newFollowup = null;

    public List<NewFollowup> getNewFollowup() {
        return newFollowup;
    }

    public void setNewFollowup(List<NewFollowup> newFollowup) {
        this.newFollowup = newFollowup;
    }

}