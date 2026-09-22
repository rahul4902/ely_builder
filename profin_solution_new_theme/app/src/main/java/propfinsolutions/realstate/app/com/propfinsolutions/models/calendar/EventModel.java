package propfinsolutions.realstate.app.com.propfinsolutions.models.calendar;

import java.time.LocalDate;
import java.time.LocalTime;
import java.util.ArrayList;

public class EventModel {
    public static ArrayList<EventModel> eventsList = new ArrayList<>();

    public static ArrayList<EventModel> eventsForDate(LocalDate date)
    {
        ArrayList<EventModel> events = new ArrayList<>();

        for(EventModel event : eventsList)
        {
            if(event.getDate().equals(date))
                events.add(event);
        }

        return events;
    }


    private String name;
    private LocalDate date;
    private LocalTime time;
    private String contactNo;
    private String leadId;

    public EventModel(String name, LocalDate date, LocalTime time, String contactNo, String leadId)
    {
        this.name = name;
        this.date = date;
        this.time = time;
        this.contactNo = contactNo;
        this.leadId = leadId;
    }

    public String getLeadId() {
        return leadId;
    }

    public void setLeadId(String leadId) {
        this.leadId = leadId;
    }

    public String getName()
    {
        return name;
    }

    public void setName(String name)
    {
        this.name = name;
    }

    public String getContactNo() {
        return contactNo;
    }

    public void setContactNo(String contactNo) {
        this.contactNo = contactNo;
    }

    public LocalDate getDate()
    {
        return date;
    }

    public void setDate(LocalDate date)
    {
        this.date = date;
    }

    public LocalTime getTime()
    {
        return time;
    }

    public void setTime(LocalTime time)
    {
        this.time = time;
    }
}


