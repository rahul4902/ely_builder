package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import android.animation.ObjectAnimator;
import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;

import androidx.cardview.widget.CardView;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.animation.LinearInterpolator;
import android.widget.Button;
import android.widget.TextView;

import com.google.android.material.floatingactionbutton.FloatingActionButton;

import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.DashboardAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.TodayListStatusAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModelStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.checkInOut.CheckInOutModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.LeadCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.RecordCount;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.Today_count;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.TotalCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.HomeActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.LeadListActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.NewLeadActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.PushNotificationActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewDetailActivityOne;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.checkIn.CheckInOutActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class DashboardFragment extends Fragment implements View.OnClickListener,DashboardAdapter.ItemListener, TodayListStatusAdapter.ItemListener {

    TextView checkIn, checkOut, followUp, marqueeText;
    FloatingActionButton createLead;
    RecyclerView recyclerViewLeadSummary, recyclerViewTodayLeads;
    private String key1;
    DialogClass dialogClass;
    int total , open , progress , close ;
    ArrayList<DataModel> arrayList;
    ArrayList<DataModelStatus> TodaySta;
    ArrayList<CheckInOutModel.Empcheckindata> checkInListData;
    String date;
    String checkStatusCheckIn;
    CardView llFollowUp;
    private static final String TAG = HomeFragment.class.getSimpleName();
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view =  inflater.inflate(R.layout.fragment_dashboard, container, false);
        createLead = view.findViewById(R.id.createNewLeads);
        followUp = view.findViewById(R.id.followUpCount);
        checkIn = view.findViewById(R.id.checkIn);
        checkOut = view.findViewById(R.id.checkOut);
        llFollowUp = view.findViewById(R.id.followUp);
        marqueeText = view.findViewById(R.id.marqueeText);

        recyclerViewLeadSummary = view.findViewById(R.id.recyclerViewLeadSummary);
        recyclerViewTodayLeads = view.findViewById(R.id.recyclerViewTodayLead);
        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(requireActivity());

        // Now we will call setSelected() method
        // and pass boolean value as true
        marqueeText.setSelected(true);

        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        Log.v("key","key1"+key1);

        date = new SimpleDateFormat("yyyy-MM-dd").format(new Date());

        createLead.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                // Create an ObjectAnimator to rotate the FAB
                ObjectAnimator rotateAnimator = ObjectAnimator.ofFloat(createLead, "rotation", 0f, 360f);
                rotateAnimator.setDuration(300); // Duration for the animation (in milliseconds)
                rotateAnimator.setInterpolator(new LinearInterpolator()); // Smooth rotation
                rotateAnimator.start();
                Navigatior.getClassInstance().navigateToActivityWithContext(requireActivity(), NewLeadActivity.class);
            }
        });

        checkINStatus(key1, date);

        checkIn.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                checkStatusCheckIn = "checkIn";
                Intent intent = new Intent(requireActivity(), CheckInOutActivity.class);
                intent.putExtra("checkData", checkStatusCheckIn);
                startActivity(intent);
            }
        });

        checkOut.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                checkStatusCheckIn = "checkOut";
                Intent intent = new Intent(requireActivity(), CheckInOutActivity.class);
                intent.putExtra("checkData", checkStatusCheckIn);
                startActivity(intent);
            }
        });

        llFollowUp.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
                new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(8);
            }
        });
        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        callLeadCountApi(key1);
        callLeadTodayApi(key1);
        checkINStatus(key1, date);
    }

    public void callLeadCountApi(String key){

        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("key","key"+key);
        apiInterface.getLeadCount(key).enqueue(new Callback<LeadCountModel>() {
            @Override
            public void onResponse(Call<LeadCountModel> call, Response<LeadCountModel> response) {
                if (response.isSuccessful()) {
                    try {
                        //        new DialogClass(HomeActivity.this).stopProgress();
                        List<RecordCount> recordCount = response.body().getRecordCount();

                        if (recordCount != null){
                            Log.d("TOTLE LEAD Count","total"+recordCount);

                            total = recordCount.get(0).getTotal();

                            Log.d("TOTLE LEAD Count","total"+total);


                            open = recordCount.get(0).getOpen();

                            Log.v("OPEN", String.valueOf(open));
                            progress = recordCount.get(0).getProgress();
                            close = recordCount.get(0).getClose();
                            new SharedPrefClass(requireActivity().getApplicationContext()).totalLeadDetails(total,open,progress,close);

                            arrayList = new ArrayList<>();
                            arrayList.add(new DataModel("Total Leads", new SharedPrefClass(requireActivity().getApplicationContext()).getLotal_lead(),R.drawable.img_total, "#ffffff"));
                            // arrayList.add(new DataModel("Total Leads", 500,R.drawable.img_total, "#00c0ef"));
                            arrayList.add(new DataModel("Lead Open", new SharedPrefClass(requireActivity().getApplicationContext()).getOpen_lead(),R.drawable.lead_status_open, "#ffffff"));

                            Log.v("Lead Open", String.valueOf(new SharedPrefClass(requireActivity().getApplicationContext()).getOpen_lead()));
                            arrayList.add(new DataModel("Lead In Process",new SharedPrefClass(requireActivity().getApplicationContext()).getProgress_lead(), R.drawable.lead_status_pending, "#ffffff"));
                            arrayList.add(new DataModel("Lead Closed",new SharedPrefClass(requireActivity().getApplicationContext()).getClose_lead(), R.drawable.lead_status_close, "#ffffff"));

                            GridLayoutManager manager = new GridLayoutManager(requireActivity(),  2);
                            recyclerViewLeadSummary.setLayoutManager(manager);

                            DashboardAdapter adapter = new DashboardAdapter(requireActivity().getApplicationContext(), arrayList, DashboardFragment.this);
                            recyclerViewLeadSummary.setAdapter(adapter);
                        }


                    }catch (NullPointerException npe)
                    {
                        dialogClass.stopProgress();
                        Log.e(TAG, "Error : "+npe, npe);
                    }

                }
                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<LeadCountModel> call, Throwable t) {
                dialogClass.stopProgress();
            }
        });
    }

        public void callLeadTodayApi(String key){

        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);


        Log.v("key","key"+key);
        apiInterface.getLeadTotal(key).enqueue(new Callback<TotalCountModel>() {
            @Override
            public void onResponse(Call<TotalCountModel> call, Response<TotalCountModel> response) {
                if (response.isSuccessful()) {
                    try {
                        //        new DialogClass(HomeActivity.this).stopProgress();
                        List<Today_count> today_counts = response.body().getToday_count();

                        if (today_counts != null){
                            followUp.setText(String.valueOf(today_counts.get(0).getTodayPending()));

                            TodaySta = new ArrayList<>();
                            TodaySta.add(new DataModelStatus("Total", today_counts.get(0).getTodayTotal(),R.drawable.lead_status_total, "#ffffff"));
                            TodaySta.add(new DataModelStatus("Pending",today_counts.get(0).getTodayPending(), R.drawable.lead_status_open, "#ffffff"));//00a65a

                            TodaySta.add(new DataModelStatus("Today activity", today_counts.get(0).getTodayOpen(),R.drawable.lead_status_pending, "#ffffff"));
                            //   TodaySta.add(new DataModelStatus("Open", 100,R.drawable.lead_status_open, "#00a65a"));
                            TodaySta.add(new DataModelStatus("Closed",today_counts.get(0).getTodayClose(), R.drawable.lead_status_close, "#ffffff"));

                            GridLayoutManager manager = new GridLayoutManager(requireActivity(),  2);
                            recyclerViewTodayLeads.setLayoutManager(manager);

                            TodayListStatusAdapter todayStaus = new TodayListStatusAdapter(requireActivity().getApplicationContext(), TodaySta, DashboardFragment.this);
                            recyclerViewTodayLeads.setAdapter(todayStaus);
                        }


                    }catch (NullPointerException npe)
                    {
                        dialogClass.stopProgress();
                        Log.e(TAG, "Error : "+npe, npe);
                    }
                }
                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<TotalCountModel> call, Throwable t) {
                dialogClass.stopProgress();
                Log.d("Dadhboard", "response error" + t.getMessage());
            }
        });
    }

    public void checkINStatus(String key, String date) {

        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.check_in_status(key, date).enqueue(new Callback<CheckInOutModel>() {
            @Override
            public void onResponse(Call<CheckInOutModel> call, Response<CheckInOutModel> response) {
               if (response.isSuccessful() && response.body() != null){
                   List<CheckInOutModel.Empcheckindata> checkInStatus = response.body().getData().getEmpcheckindata();

                   if (checkInStatus!= null){
                       checkInListData = new ArrayList<>();
                       checkInListData.addAll(checkInStatus);
                       checkIn.setVisibility(View.VISIBLE);
                       if (checkInListData.size() == 1) {
                           // Case when the list has only 1 item
                           if (checkInListData.get(0).getCheckOutAddress().isEmpty()
                                   && checkInListData.get(0).getCheckOutLat().equals("0")
                                   && checkInListData.get(0).getCheckOutLng().equals("0")
                                   && checkInListData.get(0).getCheckOutDate() == null) {

                               checkIn.setVisibility(View.GONE);
                               checkOut.setVisibility(View.VISIBLE);
                               checkStatusCheckIn = "checkOut";
                           } else {
                               checkOut.setVisibility(View.GONE);
                               checkIn.setVisibility(View.VISIBLE);
                               checkStatusCheckIn = "checkIn";
                           }
                       } else if (checkInListData.size() > 1) {
                           // Case when the list has more than 1 item (check the last item)
                           int lastIndex = checkInListData.size() - 1;

                           if (checkInListData.get(lastIndex).getCheckOutAddress().isEmpty()
                                   && checkInListData.get(lastIndex).getCheckOutLat().equals("0")
                                   && checkInListData.get(lastIndex).getCheckOutLng().equals("0")
                                   && checkInListData.get(lastIndex).getCheckOutDate() == null) {

                               checkIn.setVisibility(View.GONE);
                               checkOut.setVisibility(View.VISIBLE);
                               checkStatusCheckIn = "checkOut";
                           } else {
                               checkOut.setVisibility(View.GONE);
                               checkIn.setVisibility(View.VISIBLE);
                               checkStatusCheckIn = "checkIn";
                           }
                       } else {
                           // Handle the case when the list is empty if needed
                           // No data available, you can set default visibility or other actions here
                           checkOut.setVisibility(View.GONE);
                           checkIn.setVisibility(View.VISIBLE);
                           checkStatusCheckIn = "checkIn";
                       }

                   }
               }

            }
            @Override
            public void onFailure(Call<CheckInOutModel> call, Throwable t) {

//                    dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
                Log.d("onFailure", "failed : "+ t.getMessage());
            }
        });
    }

    @Override
    public void onClick(View v) {

    }

    @Override
    public void onItemClick(DataModel item) {

        Log.d("IEMS_data",item.toString());


        if(item.text.equals("Total Leads"))
        {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(3);
        }
        else if(item.text.equals("Lead Open")) {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(1);

        }
        else if(item.text.equals("Lead In Process")) {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(4);

        }

        else if(item.text.equals("Lead Closed"))
        {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(2);
        }
    }

    @Override
    public void onItemClick(DataModelStatus item) {

        if(item.text.equals("Total"))
        {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(6);

        }
        else if(item.text.equals("Pending")) {

            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(8);

        }
        else if(item.text.equals("Today activity")) {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(7);
        }
        else if(item.text.equals("Closed"))
        {
            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(9);

        }

    }
}