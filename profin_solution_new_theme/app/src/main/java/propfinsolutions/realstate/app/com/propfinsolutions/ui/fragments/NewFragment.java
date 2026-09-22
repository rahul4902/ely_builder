package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.NonNull;
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
import android.widget.LinearLayout;
import android.widget.Toast;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.interfaces.RecyclerOnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.DashboardAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.LeadsFilterAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calllog.AddCallLogs;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.LeadCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.RecordCount;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.Today_count;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.LoginModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.HomeActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.LeadListActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewDetailActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewEditDetails;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.PaginationHelper;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import propfinsolutions.realstate.app.com.propfinsolutions.view_detail.ViewDetailFragment;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class NewFragment extends Fragment implements RecyclerOnClick {
    private RecyclerView newLeadsRecycler;
    LinearLayoutManager layoutManager;
    private LinearLayout llNoDataFound;
    DialogClass dialogClass;
    private String key1;
    private ArrayList<LeadsFilter.LeadList> arrayList;
    LeadsFilterAdapter adapter;
    private static final String TAG = HomeFragment.class.getSimpleName();
    private long callStartTime = 0;
    private long callEndTime = 0;
    private String contactNumber;
    private String leadId;
    private TelephonyManager telephonyManager;
    private PhoneStateListener phoneStateListener;
    private boolean isCallConnected = false;
    private boolean loading = true;
    private TelecomManager telecomManager;
    private android.telecom.Call.Callback callCallback;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_new, container, false);

        newLeadsRecycler = view.findViewById(R.id.newLeadRecycler);
        llNoDataFound = view.findViewById(R.id.llNoDataFound);

        // Initialize the adapter once, and update it later

        dialogClass = DialogClass.getInstance();

        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        Log.v("key","key1 : "+key1);

        layoutManager = new LinearLayoutManager(requireActivity().getApplicationContext());
        newLeadsRecycler.setLayoutManager(layoutManager);

        callNewLeadFilterApi(key1, "new");

        PaginationHelper.setUpPagination(newLeadsRecycler, layoutManager, loading, () -> {
            // Logic to load more data
            Toast.makeText(requireActivity().getApplicationContext(), "Loading more data...", Toast.LENGTH_SHORT).show();

            // Call your API to load more data
            callNewLeadFilterApi(key1, "new");

            // Notify adapter
            adapter.notifyDataSetChanged();

            // Reset loading flag
            loading = true;
        });



        // Initialize TelephonyManager
        telephonyManager = (TelephonyManager) requireContext().getSystemService(Context.TELEPHONY_SERVICE);
        // Initialize the PhoneStateListener
//        phoneStateListener = new PhoneStateListener() {
//            @Override
//            public void onCallStateChanged(int state, String incomingNumber) {
//                switch (state) {
//                    case TelephonyManager.CALL_STATE_OFFHOOK:
//                        if (!isCallConnected) {
//                            // Call is now connected
//                            callStartTime = System.currentTimeMillis();
//                            isCallConnected = true;
//                            Log.d("Call Info", "Call connected at: " + callStartTime);
//                        }
//                        break;
//
//                    case TelephonyManager.CALL_STATE_IDLE:
//                        if (isCallConnected) {
//                            // Call ended, calculate duration
//                            callEndTime = System.currentTimeMillis();
//                            long duration = (callEndTime - callStartTime) / 1000; // in seconds
//                            Log.d("Call Info", "Call ended at: " + callEndTime);
//                            Log.d("Call Info", "Call duration: " + duration + " seconds");
//
//                            // Reset values
//                            isCallConnected = false;
//                            callStartTime = 0;
//                        }
//                        break;
//
//                    case TelephonyManager.CALL_STATE_RINGING:
//                        // Optional: Handle incoming call ring state
//                        break;
//
//                    default:
//                        Log.d("Call Info", "Unknown call state: " + state);
//                }
//            }
//        };

        // Register PhoneStateListener to monitor call states
        registerPhoneStateListener();

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

       // callNewLeadFilterApi(key1, "new");
    }

    @Override
    public void onPause() {
        super.onPause();
        // Unregister the listener in onPause to avoid memory leaks
        TelephonyManager telephonyManager = (TelephonyManager) requireContext().getSystemService(Context.TELEPHONY_SERVICE);
        telephonyManager.listen(phoneStateListener, PhoneStateListener.LISTEN_NONE);
        Log.d("PhoneStateListener", "Listener unregistered.");
    }

    public void callNewLeadFilterApi(String key, String status) {
        dialogClass.startProgress(requireActivity());
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.getLeadFilterList(key, status).enqueue(new Callback<LeadsFilter>() {
            @Override
            public void onResponse(Call<LeadsFilter> call, Response<LeadsFilter> response) {
                try {
                    if (response.isSuccessful() && response.body() != null) {
                        List<LeadsFilter.LeadList> leadList = response.body().getLeadList();

                        if (leadList == null || leadList.isEmpty()) {
                            llNoDataFound.setVisibility(View.VISIBLE);
                            newLeadsRecycler.setVisibility(View.GONE);
                        } else {
                            llNoDataFound.setVisibility(View.GONE);
                            newLeadsRecycler.setVisibility(View.VISIBLE);
                            arrayList = new ArrayList<>();
                            // Clear the old data and add new data
                            arrayList.clear();
                            arrayList.addAll(leadList);

                            adapter = new LeadsFilterAdapter(requireActivity().getApplicationContext(), arrayList, NewFragment.this, false);
                            newLeadsRecycler.setAdapter(adapter);
                            adapter.notifyDataSetChanged();  // Notify the adapter of the data change
                        }
                    } else {
                        llNoDataFound.setVisibility(View.VISIBLE);
                        newLeadsRecycler.setVisibility(View.GONE);
                    }
                }catch (Exception e){
                    Log.d("New Lead ", "failed : "+ e.getMessage());
                }

                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<LeadsFilter> call, Throwable t) {
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
    @Override
    public void onItemClick(LeadsFilter.LeadList item) {
        Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
        new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(3);
    }
    @Override
    public void onItemCalendarClick(LeadsFilter.LeadList item) {

        int leadID = Integer.parseInt(item.getId());
        Intent intent=new Intent(requireActivity().getApplicationContext(), ViewDetailFragment.class);
        SharedPreferences pref=requireActivity().getApplicationContext().getSharedPreferences("Status", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor=pref.edit();
        editor.putString("Status",item.getStatus());
        editor.commit();
        new SharedPrefClass(requireActivity().getApplicationContext()).setLeadID(item.getId());
        Navigatior.getClassInstance().navigateToActivityWithDataNotAnim(requireActivity(), ViewDetailActivity.class, null, leadID);
    }
    @Override
    public void onItemChatClick(LeadsFilter.LeadList item) {
        Intent intent = new Intent(Intent.ACTION_VIEW);
        intent.setData(Uri.parse("http://api.whatsapp.com/send?phone=" + "+91" + item.getContactNo()));
        startActivity(intent);
    }
    @Override
    public void onItemCallClick(LeadsFilter.LeadList item) {
        contactNumber = item.getContactNo(); // Save contact number for later use
        leadId = item.getId(); // Save contact number for later use

        if (ContextCompat.checkSelfPermission(getContext(), android.Manifest.permission.CALL_PHONE) != PackageManager.PERMISSION_GRANTED ||
                ContextCompat.checkSelfPermission(getContext(), android.Manifest.permission.READ_PHONE_STATE) != PackageManager.PERMISSION_GRANTED) {
            ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.CALL_PHONE, android.Manifest.permission.READ_PHONE_STATE}, 1);
        } else {
            initiateCall(contactNumber);  // Call if permissions are already granted
        }
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

}