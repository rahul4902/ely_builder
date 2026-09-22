package propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList;

/**
 * Created by Divakar on 7/10/2017.
 */

public class LeadListPassingModel {

    public LeadListPassingModel(String mobile,String lead_id, String name, String email, String source, String location, String project, String budget, String requirement,String status, String nextFollowUp) {
        this.lead_id = lead_id;
        this.mobile = mobile;
        this.name = name;
        this.email = email;
        this.source = source;
        this.location = location;
        this.project = project;
        this.budget = budget;
        this.requirement = requirement;
        this.status = status;
        this.nextFollowUp = nextFollowUp;
    }

    public String getLeadId() {
        return lead_id;
    }

    public void setLeadId(String lead_id) {
        this.lead_id = lead_id;
    }

    public String getName() {
        return name;
    }

    public String getMobile() {
        return mobile;
    }

    public void setMobile(String mobile) {
        this.mobile = mobile;
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

    public String getLead_id() {
        return lead_id;
    }

    public void setLead_id(String lead_id) {
        this.lead_id = lead_id;
    }

    public String getStatus() {
        return status;
    }

    public void setStatus(String status) {
        this.status = status;
    }

    public void setNextFollowUp(String nextFollowUp) {
        this.nextFollowUp = nextFollowUp;
    }


    String mobile,lead_id, name, email, source, location, project, budget, requirement,status, nextFollowUp;


}
