package propfinsolutions.realstate.app.com.propfinsolutions.lead_list;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageButton;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.core.net.ParseException;
import androidx.recyclerview.widget.RecyclerView;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.interfaces.RecyclerOnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;

public class LeadsFilterAdapter extends RecyclerView.Adapter<LeadsFilterAdapter.LeadListViewHolder> {
    Context mContext;
    ArrayList<LeadsFilter.LeadList> list;
    RecyclerOnClick onItemClick;
    boolean isVisitFragment;

    public LeadsFilterAdapter(Context mContext, ArrayList<LeadsFilter.LeadList> list, RecyclerOnClick onItemClick, boolean isVisitFragment) {
        this.mContext = mContext;
        this.list = list;
        this.onItemClick = onItemClick;
       this.isVisitFragment = isVisitFragment;
    }
    @NonNull
    @Override
    public LeadListViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(mContext).inflate(R.layout.new_leads_list_item, parent, false);
        return new LeadListViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull LeadListViewHolder holder, int position) {
        LeadsFilter.LeadList item = list.get(position);

        if (item != null) {
            holder.nameText.setText(item.getName() != null ? item.getName() : "");
            holder.city.setText(item.getCity() != null ? item.getCity() : "");
            holder.projectText.setText(item.getProject() != null ?
                    item.getProject() +" (" + item.getRequirement() + ")"
                    : "");
            holder.numberText.setText(
                    item.getContactNo() != null ?
                           " ("+ item.getContactNo() + ")"
                            : "");

           // holder.followUpText.setText(item.getNextfollowup() != null ? "Next Follow Up " +  item.getNextfollowup() : "");

            SimpleDateFormat inputFormat = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
            SimpleDateFormat outputFormat = new SimpleDateFormat("dd MMMM yyyy", Locale.getDefault());
            String nextFollowUp = item.getNextfollowup();  // Get the date string
            String meetingDone = item.getMeetingdate();  // Get the date string
            String formattedDate = "";
            String meetingDate = "";

            try {
                if (nextFollowUp != null) {
                    Date date = inputFormat.parse(nextFollowUp);  // Parse the input date
                    if (date != null) {
                        formattedDate = outputFormat.format(date);  // Format to the desired output
                    }
                }

                if (meetingDone != null) {
                    Date date = inputFormat.parse(meetingDone);  // Parse the input date
                    if (date != null) {
                        meetingDate = outputFormat.format(date);  // Format to the desired output
                    }
                }
            } catch (ParseException | java.text.ParseException e) {
                e.printStackTrace();
            }

            holder.followUpText.setText(!formattedDate.isEmpty() ? "Next Follow Up: " + formattedDate : "");
            // Set the text based on whether it's MeetingFragment or VisitFragment
            if (isVisitFragment) {
                holder.meetingText.setText(!meetingDate.isEmpty() ? "Visit: " + meetingDate : "");
            } else {
                holder.meetingText.setText(!meetingDate.isEmpty() ? "Meeting: " + meetingDate : "");
            }
            holder.arrowButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onItemClick.onItemClick(item);
                }
            });
            holder.calendarButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onItemClick.onItemCalendarClick(item);
                }
            });
            holder.chatButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onItemClick.onItemChatClick(item);
                }
            });
            holder.callButton.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    onItemClick.onItemCallClick(item);
                }
            });
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public class LeadListViewHolder extends RecyclerView.ViewHolder{
        TextView nameText, city, projectText, numberText, followUpText, meetingText;
        ImageButton arrowButton, calendarButton, chatButton, callButton;
        public LeadListViewHolder(@NonNull View itemView) {
            super(itemView);
            nameText = itemView.findViewById(R.id.nameText);
            projectText = itemView.findViewById(R.id.projectText);
            numberText = itemView.findViewById(R.id.numberText);
            city = itemView.findViewById(R.id.cityText);
            arrowButton = itemView.findViewById(R.id.arrowButton);
            calendarButton = itemView.findViewById(R.id.calendarButton);
            callButton = itemView.findViewById(R.id.callButton);
            chatButton = itemView.findViewById(R.id.chatButton);
            followUpText = itemView.findViewById(R.id.followUpText);
            meetingText = itemView.findViewById(R.id.meetingText);
        }
    }
}
