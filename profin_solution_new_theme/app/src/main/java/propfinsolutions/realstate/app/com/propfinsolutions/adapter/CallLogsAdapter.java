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
import propfinsolutions.realstate.app.com.propfinsolutions.models.calllog.GetCallLogsModelResponse;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;

public class CallLogsAdapter extends RecyclerView.Adapter<CallLogsAdapter.CallLogViewHolder> {
    Context mContext;
    ArrayList<GetCallLogsModelResponse.Calllogs> list;

    public CallLogsAdapter(Context mContext, ArrayList<GetCallLogsModelResponse.Calllogs> list) {
        this.mContext = mContext;
        this.list = list;
    }

    @NonNull
    @Override
    public CallLogViewHolder onCreateViewHolder(@NonNull ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(parent.getContext()).inflate(R.layout.call_logs_item_list, parent, false);
        return new CallLogViewHolder(view);
    }

    @Override
    public void onBindViewHolder(@NonNull CallLogViewHolder holder, int position) {
        GetCallLogsModelResponse.Calllogs item = list.get(position);
        if (item != null) {
            holder.notifyTitle.setText(item.getName() != null ? item.getName() : "");
            holder.notifyContent.setText(item.getContactNo() != null ? item.getContactNo() : "");

            String createdAt = item.getCallStartDatetime();  // This is the "created_at" field from your response

            // Calculate and display the call duration
            String callStart = item.getCallStartDatetime();  // call_start_datetime
            String callEnd = item.getCallEndDatetime();      // call_end_datetime

            String callDuration = getCallDuration(callStart, callEnd);
            holder.textCallDuration.setText("Call Duration : "+callDuration);  // Set call duration in text view

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

    // Helper method to calculate the call duration
    private String getCallDuration(String start, String end) {
        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd HH:mm:ss", Locale.getDefault());
        try {
            Date startDate = sdf.parse(start);
            Date endDate = sdf.parse(end);

            long durationInMillis = endDate.getTime() - startDate.getTime();
            long seconds = TimeUnit.MILLISECONDS.toSeconds(durationInMillis) % 60;
            long minutes = TimeUnit.MILLISECONDS.toMinutes(durationInMillis) % 60;
            long hours = TimeUnit.MILLISECONDS.toHours(durationInMillis);

            if (hours > 0) {
                return hours + "h " + minutes + "m " + seconds + "s";
            } else if (minutes > 0) {
                return minutes + "m " + seconds + "s";
            } else {
                return seconds + "s";
            }
        } catch (ParseException | java.text.ParseException e) {
            e.printStackTrace();
            return "Invalid duration";
        }
    }

    @Override
    public int getItemCount() {
        return list.size();
    }

    public class CallLogViewHolder extends RecyclerView.ViewHolder{

        TextView notifyTitle, notifyContent, textDate, textCallDuration;

        public CallLogViewHolder(@NonNull View itemView) {
            super(itemView);

            notifyTitle = itemView.findViewById(R.id.notifyTitle);
            notifyContent = itemView.findViewById(R.id.notifyContent);
            textDate = itemView.findViewById(R.id.textDate);
            textCallDuration = itemView.findViewById(R.id.textCallDuration);
        }
    }
}
