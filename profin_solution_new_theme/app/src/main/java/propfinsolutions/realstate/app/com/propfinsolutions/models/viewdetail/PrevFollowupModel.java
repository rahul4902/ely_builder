package propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail;

/**
 * Created by Divakar on 7/13/2017.
 */


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class PrevFollowupModel {

    @SerializedName("PreviousFollowup")
    @Expose
    private List<PreviousFollowup> previousFollowup = null;



    @SerializedName("message")
    @Expose
    private List<PreviousFollowup> noFollowup;

    public List<PreviousFollowup> getNoFollowup() {
        return noFollowup;
    }

    public List<PreviousFollowup> getPreviousFollowup() {
        return previousFollowup;
    }

    public void setPreviousFollowup(List<PreviousFollowup> previousFollowup) {
        this.previousFollowup = previousFollowup;
    }

}