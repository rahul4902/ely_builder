package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.calendar;

import static propfinsolutions.realstate.app.com.propfinsolutions.utils.CalendarUtils.daysInWeekArray;
import static propfinsolutions.realstate.app.com.propfinsolutions.utils.CalendarUtils.monthYearFromDate;

import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.telecom.TelecomManager;
import android.telephony.PhoneStateListener;
import android.telephony.TelephonyManager;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;

import java.text.SimpleDateFormat;
import java.time.LocalDate;
import java.time.LocalDateTime;
import java.time.LocalTime;
import java.time.format.DateTimeFormatter;
import java.time.format.DateTimeParseException;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.calendar.CalendarDayAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.calendar.EventAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.interfaces.EventCallOnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.LeadsFilterAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calendar.CalendarUserFollowUpRes;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calendar.EventModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calllog.AddCallLogs;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.NewFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.CalendarUtils;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CalendarFragment extends Fragment implements CalendarDayAdapter.OnItemListener, EventCallOnClick {
    private TextView monthYearText;
    private RecyclerView calendarRecyclerView;
    private ListView eventListView;
    Button nextWeek, previousWeek;
    DialogClass dialogClass;
    private EventAdapter eventAdapter;
    private String key1;
    private long callStartTime = 0;
    private long callEndTime = 0;
    private String contactNumber;
    private String leadId;
    private TelephonyManager telephonyManager;
    private PhoneStateListener phoneStateListener;
    private boolean isCallConnected = false;
    private TelecomManager telecomManager;
    private android.telecom.Call.Callback callCallback;


    @Nullable
    @Override
    public View onCreateView(@NonNull LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        View view = inflater.inflate(R.layout.fragment_calendar, container, false);

        calendarRecyclerView = view.findViewById(R.id.calendarRecyclerView);
        monthYearText = view.findViewById(R.id.monthYearTV);
        eventListView = view.findViewById(R.id.eventListView);
        nextWeek = view.findViewById(R.id.nextWeek);
        previousWeek = view.findViewById(R.id.previousWeek);
        CalendarUtils.selectedDate = LocalDate.now();

        // Initialize the event adapter with an empty list initially
        eventAdapter = new EventAdapter(requireActivity().getApplicationContext(), new ArrayList<>(), CalendarFragment.this);
        eventListView.setAdapter(eventAdapter);

        setWeekView();

        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(requireActivity());
        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        Log.v("key","key1 : "+key1);

        callCalendarFollowUpApi(key1);
        telephonyManager = (TelephonyManager) requireContext().getSystemService(Context.TELEPHONY_SERVICE);

        registerPhoneStateListener();

        nextWeek.setOnClickListener(v -> nextWeekAction(v));
        previousWeek.setOnClickListener(v -> previousWeekAction(v));
        return view;
    }

    private void registerPhoneStateListener() {
        phoneStateListener = new PhoneStateListener() {
            @Override
            public void onCallStateChanged(int state, String incomingNumber) {
                super.onCallStateChanged(state, incomingNumber);

                Log.d("Call State", "State: " + state);

                switch (state) {
                    case TelephonyManager.CALL_STATE_OFFHOOK:
                        if (!isCallConnected) {
                            // Call connected
                            callStartTime = System.currentTimeMillis();
                            isCallConnected = true;
                            Log.d("Call Info", "Call connected at: " + callStartTime);
                        }
                        break;

                    case TelephonyManager.CALL_STATE_IDLE:
                        if (isCallConnected) {
                            // Call ended
                            callEndTime = System.currentTimeMillis();
                            long callDuration = (callEndTime - callStartTime) / 1000; // duration in seconds
                            Log.d("Call Info", "Call ended at: " + callEndTime);
                            Log.d("Call Info", "Call duration: " + callDuration + " seconds");
                            isCallConnected = false;
                            // Call the addCallLog() function to post the call log data
                            addCall(callStartTime, callEndTime,leadId,  key1);
                        }
                        break;

                    case TelephonyManager.CALL_STATE_RINGING:
                        // Phone is ringing (incoming call)
                        Log.d("Call Info", "Incoming call from: " + incomingNumber);
                        break;
                }
            }
        };

        if (ActivityCompat.checkSelfPermission(requireContext(), android.Manifest.permission.READ_PHONE_STATE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.READ_PHONE_STATE}, 1);
        } else {
            telephonyManager.listen(phoneStateListener, PhoneStateListener.LISTEN_CALL_STATE);
        }
    }


    private void setWeekView()
    {
        monthYearText.setText(monthYearFromDate(CalendarUtils.selectedDate));
        ArrayList<LocalDate> days = daysInWeekArray(CalendarUtils.selectedDate);

        CalendarDayAdapter calendarAdapter = new CalendarDayAdapter(days, CalendarFragment.this);
        RecyclerView.LayoutManager layoutManager = new GridLayoutManager(requireActivity().getApplicationContext(), 7);
        calendarRecyclerView.setLayoutManager(layoutManager);
        calendarRecyclerView.setAdapter(calendarAdapter);
        setEventAdpater();
    }

    public void previousWeekAction(View view)
    {
        CalendarUtils.selectedDate = CalendarUtils.selectedDate.minusWeeks(1);
        setWeekView();
    }

    public void nextWeekAction(View view)
    {
        CalendarUtils.selectedDate = CalendarUtils.selectedDate.plusWeeks(1);
        setWeekView();
    }

    @Override
    public void onItemClick(int position, LocalDate date)
    {
        CalendarUtils.selectedDate = date;
        setWeekView();
    }

    @Override
    public void onDestroy() {
        super.onDestroy();
        // Unregister the listener when the fragment is destroyed
        telephonyManager.listen(phoneStateListener, PhoneStateListener.LISTEN_NONE);
    }
    @Override
    public void onResume() {
        super.onResume();
        // Register the listener in onResume
        if (ContextCompat.checkSelfPermission(requireContext(), android.Manifest.permission.READ_PHONE_STATE) == PackageManager.PERMISSION_GRANTED) {
            TelephonyManager telephonyManager = (TelephonyManager) requireContext().getSystemService(Context.TELEPHONY_SERVICE);
            telephonyManager.listen(phoneStateListener, PhoneStateListener.LISTEN_CALL_STATE);
            Log.d("PhoneStateListener", "Listener registered.");
        } else {
            Log.d("PhoneStateListener", "READ_PHONE_STATE permission not granted.");
        }

        setEventAdpater();
        callCalendarFollowUpApi(key1);
    }

    @Override
    public void onPause() {
        super.onPause();
        // Unregister the listener in onPause to avoid memory leaks
        TelephonyManager telephonyManager = (TelephonyManager) requireContext().getSystemService(Context.TELEPHONY_SERVICE);
        telephonyManager.listen(phoneStateListener, PhoneStateListener.LISTEN_NONE);
        Log.d("PhoneStateListener", "Listener unregistered.");
    }

    private void setEventAdpater()
    {
        // Get the events for the selected date
        ArrayList<EventModel> dailyEvents = EventModel.eventsForDate(CalendarUtils.selectedDate);

        // Safely update the eventAdapter with new events
        if (eventAdapter != null) {
            eventAdapter.updateEvents(dailyEvents);
        } else {
            Log.e("CalendarFragment", "EventAdapter is null");
        }
//        ArrayList<EventModel> dailyEvents = EventModel.eventsForDate(CalendarUtils.selectedDate);
////        EventAdapter eventAdapter = new EventAdapter(requireActivity().getApplicationContext(), dailyEvents);
////        eventListView.setAdapter(eventAdapter);
//        eventAdapter.updateEvents(dailyEvents);
   }

    public void newEventAction(View view)
    {
      //  startActivity(new Intent(this, EventEditActivity.class));
    }

    public void callCalendarFollowUpApi(String key) {

        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.getCalendarFollowUp(key).enqueue(new Callback<CalendarUserFollowUpRes>() {
            @Override
            public void onResponse(Call<CalendarUserFollowUpRes> call, Response<CalendarUserFollowUpRes> response) {

                try {
                    if (response.isSuccessful() && response.body() != null) {
                        List<CalendarUserFollowUpRes.Userfollowups> leadList = response.body().getUserfollowups();
                        EventModel.eventsList.clear();

                        if (leadList != null || !leadList.isEmpty()){
                            for (CalendarUserFollowUpRes.Userfollowups lead : leadList) {
                                String name = lead.getName();
                                String contactNo = lead.getContactNo();
                                String leadId = lead.getLeadId();
                                String followUpDateStr = lead.getFollowUpDate(); // From the API
                                LocalDate date;
                                LocalTime time;

                                try {
                                    DateTimeFormatter formatter = DateTimeFormatter.ofPattern("yyyy-MM-dd HH:mm:ss"); // Adjust based on API format
                                    LocalDateTime dateTime = LocalDateTime.parse(followUpDateStr, formatter);
                                    date = dateTime.toLocalDate();
                                    time = dateTime.toLocalTime();

                                    // Create and add event to the list
                                    EventModel newEvent = new EventModel(name, date, time, contactNo, leadId);
                                    EventModel.eventsList.add(newEvent);
                                } catch (DateTimeParseException e) {
                                    e.printStackTrace();
                                    Log.e("CalendarFragment", "Failed to parse date: " + followUpDateStr);
                                }
                            }
                            // Now update the event adapter for the current selected date
                            setEventAdpater();
                        }

                    } else {
                        Log.d("CalendarFragment", "Failed to parse date: ");
                    }

                }catch (Exception e){
                    Log.d("Calendar fragment", "failed : "+ e.getMessage());
                }


                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<CalendarUserFollowUpRes> call, Throwable t) {
                dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
                Log.d("onFailure", "failed : "+ t.getMessage());
            }
        });
    }

    private void addCall(long callStartTime, long callEndTime, String lead_id, String key ) {

        SimpleDateFormat sdf = new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss", Locale.getDefault());
        String startTime = sdf.format(new Date(callStartTime));
        String endTime = sdf.format(new Date(callEndTime));
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        apiInterface.addCallLogs(startTime, endTime, lead_id, key).enqueue(new Callback<AddCallLogs>() {
            @Override
            public void onResponse(Call<AddCallLogs> call, Response<AddCallLogs> response) {
                if (response.isSuccessful() && response.body() != null){
                    Log.d("addCallLog", "Call log added successfully: " + response.body());
                }else {
                    Log.d("addCallLog", "Failed to add call log: " + response.errorBody());
                }
            }
            @Override
            public void onFailure(Call<AddCallLogs> call, Throwable t) {
                Log.d("addCallLog", "API call failed: " + t.getMessage());
            }
        });

    }

    // Method to initiate a call (in case you need it)
    private void initiateCall(String contactNumber) {
        if (ActivityCompat.checkSelfPermission(getContext(), android.Manifest.permission.CALL_PHONE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.CALL_PHONE}, 1);
        } else {
            Intent callIntent = new Intent(Intent.ACTION_CALL);
            callIntent.setData(Uri.parse("tel:" + contactNumber));
            startActivity(callIntent);
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == 1) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                // Permission granted, register the listener
                telephonyManager.listen(phoneStateListener, PhoneStateListener.LISTEN_CALL_STATE);
            } else {
                Log.d("Permission", "READ_PHONE_STATE permission denied");
            }
        }
    }

    @Override
    public void onItemCallClick(EventModel item) {
        contactNumber = item.getContactNo(); // Save contact number for later use
        leadId = item.getLeadId();

        if (ContextCompat.checkSelfPermission(getContext(), android.Manifest.permission.CALL_PHONE) != PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(getContext(), android.Manifest.permission.READ_PHONE_STATE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.CALL_PHONE, android.Manifest.permission.READ_PHONE_STATE}, 1);
        } else {
            initiateCall(contactNumber);  // Call if permissions are already granted
        }
    }
}


