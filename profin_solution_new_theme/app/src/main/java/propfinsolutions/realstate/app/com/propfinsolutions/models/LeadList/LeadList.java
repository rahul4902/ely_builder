package propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList;

/**
 * Created by Divakar on 7/10/2017.
 */



import com.google.gson.annotations.Expose;
import com.google.gson.annotations.SerializedName;

public class LeadList {
    @SerializedName("id")
    @Expose
    private String id;
    @SerializedName("name")
    @Expose
    private String name;
    @SerializedName("email")
    @Expose
    private String email;
    @SerializedName("source")
    @Expose
    private String source;
    @SerializedName("location")
    @Expose
    private String location;
    @SerializedName("project")
    @Expose
    private String project;
    @SerializedName("budget")
    @Expose
    private String budget;
    @SerializedName("requirement")
    @Expose
    private String requirement;
    @SerializedName("next_follow_up")
    @Expose
    private String nextFollowUp;
    @SerializedName("status")
    @Expose
    private String status;

    @SerializedName("contact_no")
    @Expose
    private String mobile;

    public String getMobile() {
        return mobile;
    }

    public String getStatus() {
        return status;
    }

    public String getId() {
        return id;
    }

    public void setId(String id) {
        this.id = id;
    }

    public String getName() {
        return name;
    }

    public void setName(String name) {
        this.name = name;
    }

    public String getEmail() {
        return email;
    }

    public void setEmail(String email) {
        this.email = email;
    }

    public String getSource() {
        return source;
    }

    public void setSource(String source) {
        this.source = source;
    }

    public String getLocation() {
        return location;
    }

    public void setLocation(String location) {
        this.location = location;
    }

    public String getProject() {
        return project;
    }

    public void setProject(String project) {
        this.project = project;
    }

    public String getBudget() {
        return budget;
    }

    public void setBudget(String budget) {
        this.budget = budget;
    }

    public String getRequirement() {
        return requirement;
    }

    public void setRequirement(String requirement) {
        this.requirement = requirement;
    }

    public String getNextFollowUp() {
        return nextFollowUp;
    }

    public void setNextFollowUp(String nextFollowUp) {
        this.nextFollowUp = nextFollowUp;
    }

}
