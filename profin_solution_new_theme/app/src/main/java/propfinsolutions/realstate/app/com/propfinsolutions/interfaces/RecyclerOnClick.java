package propfinsolutions.realstate.app.com.propfinsolutions.interfaces;

import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;

public interface RecyclerOnClick {
    void onItemClick(LeadsFilter.LeadList item);
     void onItemCalendarClick(LeadsFilter.LeadList item);
     void onItemChatClick(LeadsFilter.LeadList item);
     void onItemCallClick(LeadsFilter.LeadList item);
}
