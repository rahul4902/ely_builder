package propfinsolutions.realstate.app.com.propfinsolutions.master;

/**
 * Created by Divakar on 7/15/2017.
 */


import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class Requirement {

    @SerializedName("requirement_name")
    @Expose
    private String requirementName;

    public String getRequirementName() {
        return requirementName;
    }

    public void setRequirementName(String requirementName) {
        this.requirementName = requirementName;
    }

    }

