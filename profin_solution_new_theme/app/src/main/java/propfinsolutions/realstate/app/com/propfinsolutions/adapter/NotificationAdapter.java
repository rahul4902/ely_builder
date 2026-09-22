package propfinsolutions.realstate.app.com.propfinsolutions.adapter;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.TextView;

import androidx.annotation.NonNull;
import androidx.core.net.ParseException;
import androidx.recyclerview.widget.RecyclerView;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.Locale;
import java.util.concurrent.TimeUnit;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.LeadsFilterAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;

public class NotificationAdapter extends RecyclerView.Adapter<NotificationAdapter.NotifyViewHolder> {

    Context mContext;
    ArrayList<NotificationResponseModel.UserNotifications> list;

    public NotificationAdapter(Context mContext, ArrayList<NotificationResponseModel.UserNotifications> list) {
        this.mContext = mContext;
        this.list = list;
    }

    @NonNull
    @Override
    public NotifyViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.notify_item_list, parent, false);
        return new NotifyViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull NotifyViewHolder holder, int position) {
        NotificationResponseModel.UserNotifications item = list.get(position);
        if (item != null) {
            holder.notifyTitle.setText(item.getTitle() != null ? item.getTitle() : "");
            holder.notifyContent.setText(item.getContent() != null ? item.getContent() : "");

            String createdAt = item.getCreatedAt();  // This is the "created_at" field from your response

            // Parse the date using SimpleDateFormat
            SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
            try {
                Date createdDate = sdf.parse(createdAt);

                // Get current date
                Date currentDate = new Date();

                // Calculate the time difference
                long differenceInMillis = currentDate.getTime() - createdDate.getTime();

                // Check if it is within a minute, hour, day, etc.
                String relativeTime = getRelativeTimeDifference(differenceInMillis);

                // Set the relative time text to the TextView
                holder.textDate.setText(relativeTime);

            } catch (ParseException | java.text.ParseException e) {
                e.printStackTrace();
            }

        }
    }

    // Helper method to calculate and return the relative time string
    private String getRelativeTimeDifference(long differenceInMillis) {
        long minutes = TimeUnit.MILLISECONDS.toMinutes(differenceInMillis);
        long hours = TimeUnit.MILLISECONDS.toHours(differenceInMillis);
        long days = TimeUnit.MILLISECONDS.toDays(differenceInMillis);

        if (minutes < 1) {
            return "Just now";
        } else if (minutes < 60) {
            return minutes + " minutes ago";
        } else if (hours < 24) {
            return hours + " hours ago";
        } else if (days < 7) {
            return days + " days ago";
        } else if (days < 30) {
            // If the difference is less than 30 days, return weeks ago
            long weeks = days / 7;
            return weeks + (weeks == 1 ? " week ago" : " weeks ago");
        } else if (days < 365) {
            // Calculate months by dividing the days by 30
            long months = days / 30;
            return months + (months == 1 ? " month ago" : " months ago");
        } else {
            // If it's more than a year, show the full date
            SimpleDateFormat sdf = new SimpleDateFormat("MMM dd, yyyy", Locale.getDefault());
            return sdf.format(new Date(System.currentTimeMillis() - differenceInMillis));
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public class NotifyViewHolder extends RecyclerView.ViewHolder{

        TextView notifyTitle, notifyContent, textDate;

        public NotifyViewHolder(@NonNull View itemView) {
            super(itemView);

            notifyTitle = itemView.findViewById(R.id.notifyTitle);
            notifyContent = itemView.findViewById(R.id.notifyContent);
            textDate = itemView.findViewById(R.id.textDate);
        }
    }
}
