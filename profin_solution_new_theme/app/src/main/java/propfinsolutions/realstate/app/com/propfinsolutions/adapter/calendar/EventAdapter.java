package propfinsolutions.realstate.app.com.propfinsolutions.adapter.calendar;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;

import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.interfaces.EventCallOnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.interfaces.RecyclerOnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calendar.EventModel;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.CalendarUtils;

public class EventAdapter extends ArrayAdapter<EventModel> {
    private List<EventModel> events;
    EventCallOnClick onItemClick;
    public EventAdapter(@NonNull Context context, List<EventModel> events,  EventCallOnClick onItemClick) {
        super(context, 0, events);
        this.events = events;
        this.onItemClick = onItemClick;
    }
    @NonNull
    @Override
    public View getView(int position, @Nullable View convertView, @NonNull ViewGroup parent)
    {
        EventModel event = getItem(position);

        if (convertView == null)
            convertView = LayoutInflater.from(getContext()).inflate(R.layout.event_cell, parent, false);

        TextView eventCellTV = convertView.findViewById(R.id.eventCellTV);
        TextView eventUserNameTV = convertView.findViewById(R.id.eventUserNameTV);
        ImageButton callButton = convertView.findViewById(R.id.callFollowUpButton);

        String eventTitle = CalendarUtils.formattedTime(event.getTime());

        eventUserNameTV.setText(event.getName());
        eventCellTV.setText(eventTitle);

        callButton.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                onItemClick.onItemCallClick(event);
            }
        });

        return convertView;
    }
    // Method to update events in the adapter
    public void updateEvents(List<EventModel> newEvents) {
        this.events.clear();
        this.events.addAll(newEvents);
        notifyDataSetChanged(); // Notify the adapter to refresh the list
    }
}
