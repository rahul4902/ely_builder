package propfinsolutions.realstate.app.com.propfinsolutions.master;

/**
 * Created by Divakar on 7/15/2017.
 */

import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

import java.util.List;

public class MasterLeadModel {

    @SerializedName("project")
    @Expose
    private List<Project> project = null;
    @SerializedName("requirement")
    @Expose
    private List<Requirement> requirement = null;

    @SerializedName("source")
    @Expose
    private List<Source> source = null;

    @SerializedName("budget")
    @Expose
    private List<Budget> budget = null;

    public List<Source> getSource() {
        return source;
    }

    public List<Budget> getBudget() {
        return budget;
    }

    public List<Project> getProject() {
        return project;
    }

    public void setProject(List<Project> project) {
        this.project = project;
    }

    public List<Requirement> getRequirement() {
        return requirement;
    }

    public void setRequirement(List<Requirement> requirement) {
        this.requirement = requirement;
    }

}