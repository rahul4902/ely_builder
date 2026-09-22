package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import static android.content.Intent.getIntent;

import android.app.AlertDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.graphics.Typeface;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.GridLayoutManager;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;
import androidx.viewpager2.widget.ViewPager2;

import android.os.Handler;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import com.google.android.material.tabs.TabLayout;
import com.google.android.material.tabs.TabLayoutMediator;
import com.google.firebase.messaging.FirebaseMessaging;

import java.util.ArrayList;
import java.util.List;
import java.util.Objects;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.PagerApdater;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.DashboardAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.TodayListStatusAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModelStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadList;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.LeadCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.RecordCount;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.Today_count;
import propfinsolutions.realstate.app.com.propfinsolutions.models.count.TotalCountModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.Simple;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.ForegroundService;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.HomeActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.LeadListActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.AutoFitGridLayoutManager;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class HomeFragment extends Fragment
      //  implements View.OnClickListener,DashboardAdapter.ItemListener,TodayListStatusAdapter.ItemListener
{

    private static final String TAG = HomeActivity.class.getSimpleName();
    Toolbar mToolbar;
    ProgressBar totalPBar, openPBar, inProgressBar, closePBar ;
    TextView mToolbarTV, totalTV, openTV, inProgressTV, closeTV;
    Handler progressHandler = new Handler();
    int i = 0;
    String[] nameArray = {"New Lead", "Leads", "Open Lead", "Closed Lead", "Log Out"};
    //String[] nameArray = {"Aestro", "Blender", "Cupcake", "Donut", "Eclair", "Froyo", "GingerBread", "HoneyComb", "IceCream Sandwich", "JelliBean", "KitKat","Aestro", "Blender", "Cupcake", "Donut", "Eclair", "Froyo", "GingerBread", "HoneyComb", "IceCream Sandwich", "JelliBean", "KitKat", "Lollipop", "MarshMallow"};
    LinearLayout totalLayout, openLayout, inProgressLayout, closelayout;
    int total , open , progress , close ;
    DialogClass dialogClass;
    private TextView userName;
    private String key1;
//    RecyclerView recyclerView,recyclerTodayStatus;
    ArrayList<DataModel> arrayList;
    ArrayList<DataModelStatus> TodaySta;
    Context context;
    private ViewPager2 viewPager;
    private TabLayout tabLayout;
    private PagerApdater pagerApdater;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment

        View view = inflater.inflate(R.layout.fragment_home, container, false);
//        mToolbar = (Toolbar)view.findViewById(R.id.toolbar);
//        mToolbarTV = (TextView)view.findViewById(R.id.toolbar_title);
//        Intent intent= requireActivity().getIntent;
//        String leadId=intent.getStringExtra("leadId");
//        Log.v("LEADIDTEST","LEADIDTEST"+leadId);

//        if (getActivity() != null) {
//            Intent intent = requireActivity().getIntent();
//            String leadId = intent.getStringExtra("leadId");
//            Log.v("LEADIDTEST", "LEADIDTEST: " + leadId);
//
//
//            if (leadId != null) {
//                Toast.makeText(context, leadId, Toast.LENGTH_LONG).show();
//            }
//
//            FirebaseMessaging.getInstance().subscribeToTopic("leadId");
//        }

//        totalPBar = (ProgressBar) view.findViewById(R.id.pbTotal);
//        openPBar = (ProgressBar) findViewById(R.id.pbOpen);
//        inProgressBar = (ProgressBar) findViewById(R.id.pbInProgress);
//        closePBar = (ProgressBar) findViewById(R.id.pbClose);
//
//        recyclerView = (RecyclerView)view.findViewById(R.id.recyclerView);
//        recyclerTodayStatus = (RecyclerView) view.findViewById(R.id.recyclerTodayStatus);

//        GridLayoutManager gridLayoutManager = new GridLayoutManager(requireActivity().getApplicationContext(),3);
//        recyclerView.setLayoutManager(gridLayoutManager);
//
//        GridLayoutManager gridLayoutManager1 = new GridLayoutManager(context,2);
//        gridLayoutManager.setOrientation(LinearLayoutManager.HORIZONTAL);
//        recyclerTodayStatus.setLayoutManager(gridLayoutManager1);

        viewPager = view.findViewById(R.id.viewPager);
        tabLayout = view.findViewById(R.id.tabLayout);

        pagerApdater = new PagerApdater(requireActivity().getSupportFragmentManager(), getLifecycle());
        viewPager.setAdapter(pagerApdater);

        new TabLayoutMediator(tabLayout, viewPager, new TabLayoutMediator.TabConfigurationStrategy() {
            @Override
            public void onConfigureTab(@NonNull TabLayout.Tab tab, int i) {

                // Inflate the custom layout
                View tabView = LayoutInflater.from(requireContext()).inflate(R.layout.custom_tab, null);
                TextView tabTextView = tabView.findViewById(R.id.tabTextView);

                tabTextView.setText(pagerApdater.page.get(i));

                tab.setCustomView(tabView);
            }
        }).attach();

        tabLayout.addOnTabSelectedListener(new TabLayout.OnTabSelectedListener() {
            @Override
            public void onTabSelected(TabLayout.Tab tab) {
                // Find the TextView from the custom view
                TextView tabTextView = tab.getCustomView().findViewById(R.id.tabTextView);
                if (tabTextView != null) {
                    // Make the selected tab text bold
                    tabTextView.setTypeface(null, Typeface.BOLD);
                    tabTextView.setTextAppearance(requireContext(), R.style.subtitle1);
                    tabTextView.setTextColor(getResources().getColor(R.color.white));  // Optional: Change color
                }
            }

            @Override
            public void onTabUnselected(TabLayout.Tab tab) {
                // Revert to normal text style when tab is unselected
                TextView tabTextView = tab.getCustomView().findViewById(R.id.tabTextView);
                if (tabTextView != null) {
                    tabTextView.setTypeface(null, Typeface.NORMAL);
                    tabTextView.setTextAppearance(requireContext(), R.style.subtitle2);
                    tabTextView.setTextColor(getResources().getColor(R.color.date_picker_text_disabled));  // Optional: Change color back
                }
            }

            @Override
            public void onTabReselected(TabLayout.Tab tab) {
                // Handle reselection if needed
            }
        });

        TabLayout.Tab firstTab = tabLayout.getTabAt(0);
        if (firstTab != null) {
            TextView firstTabTextView = firstTab.getCustomView().findViewById(R.id.tabTextView);
            if (firstTabTextView != null) {
                firstTabTextView.setTypeface(null, Typeface.BOLD);
                firstTabTextView.setTextAppearance(requireContext(), R.style.subtitle1);
                firstTabTextView.setTextColor(getResources().getColor(R.color.white));
            }
        }


//        totalTV = (TextView) findViewById(R.id.tvTotal);
//        openTV = (TextView) findViewById(R.id.tvOpen);
//        inProgressTV = (TextView) findViewById(R.id.tvInProgress);
//        closeTV = (TextView) findViewById(R.id.tvClose);
       // userName=(TextView)view.findViewById(R.id.userName);
        ///  totalLayout = (LinearLayout)findViewById(R.id.layoutTotal);
        /*openLayout = (LinearLayout)findViewById(R.id.layoutOpen);
        inProgressLayout = (LinearLayout)findViewById(R.id.layoutInProgress);
        closelayout = (LinearLayout)findViewById(R.id.layoutClose);*/
//        String UserName = new SharedPrefClass(context).getUserName().toString();
//        if(UserName!=null) {
//            userName.setText(UserName);
//        }
        // totalLayout.setOnClickListener(this);
         /* openLayout.setOnClickListener(this);
        inProgressLayout.setOnClickListener(this);
        closelayout.setOnClickListener(this);
       */
        /**
         *  CALLING API
         */
//        dialogClass = DialogClass.getInstance();
//        dialogClass.startProgress(context);
//        key1 = new SharedPrefClass(context).getSimpleKey();
//        Log.v("key","key1"+key1);
//        callLeadCountApi(key1);
//        callLeadTodayApi(key1);

        //   callLeadTotal(key1);
        //    callLeadTotalLeadCount(key1);
        //startService();

        return view;
    }

//    private void callLeadTotalLeadCount(String key1) {
//        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
//
//        Log.v("key","key"+key1);
//        apiInterface.getLeadCount(key1).enqueue(new Callback<LeadCountModel>() {
//            @Override
//            public void onResponse(Call<LeadCountModel> call, Response<LeadCountModel> response) {
//                if (response.isSuccessful()) {
//                    try {
//                        //        new DialogClass(HomeActivity.this).stopProgress();
//                        dialogClass.stopProgress();
//                        List<RecordCount> recordCount = response.body().getRecordCount();
//
//                        if(recordCount.get(0).getTotal() != null)
//                        {
//                            Log.v("LEAD COUNT","LEAD COUNT"+recordCount.get(0).getTotal());
//
//                        }
//
////                 start();
//                    }catch (NullPointerException npe)
//                    {
//                        Log.e(TAG, "Error : "+npe, npe);
//                    }
//
//                }
//            }
//            @Override
//            public void onFailure(Call<LeadCountModel> call, Throwable t) {
//
//            }
//        });
//
//    }
//
//    public void startService() {
//        Intent serviceIntent = new Intent(requireActivity().getApplicationContext(), ForegroundService.class);
//        serviceIntent.putExtra("inputExtra", "Foreground Service Example in Android");
//
//        ContextCompat.startForegroundService(requireActivity().getApplicationContext(), serviceIntent);
//    }
//
//    public void start() {
//        startProgress(total, 1);
//        startProgress(open, 2);
//        startProgress(progress, 3);
//        startProgress(close, 4);
//    }
//
//    public void startProgress(final int i, final int leadBox) {
//        if(leadBox == 1)
//        {
//            totalPBar.setProgress(i);
//            totalTV.setText("" + i );
//            setTotalProgress(i);
//            new Simple().setTotal(i);
//        }else if (leadBox == 2)
//        {
//            openPBar.setProgress(i);
//            openTV.setText("" + i );
//            setOpenProgress(i);
//        }else if (leadBox == 3)
//        {
//            inProgressBar.setProgress(i);
//            inProgressTV.setText("" + i );
//            setInProgress(i);
//        }else if (leadBox == 4)
//        {
//            closePBar.setProgress(i);
//            closeTV.setText("" + i );
//            setCloseProgress(i);
//        }else {
//            Toast.makeText(requireActivity().getApplicationContext(), "Something went wrong", Toast.LENGTH_LONG).show();
//        }
//        //progressingTextView.setText("" + i + " %");
//    }
//
//    public void callLeadCountApi(String key){
//
//        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
//
//        Log.v("key","key"+key);
//        apiInterface.getLeadCount(key).enqueue(new Callback<LeadCountModel>() {
//            @Override
//            public void onResponse(Call<LeadCountModel> call, Response<LeadCountModel> response) {
//                if (response.isSuccessful()) {
//                    try {
//                        //        new DialogClass(HomeActivity.this).stopProgress();
//                        dialogClass.stopProgress();
//                        List<RecordCount> recordCount = response.body().getRecordCount();
//
//                        Log.d("TOTLE LEAD Count","total"+recordCount);
//
//                        total = recordCount.get(0).getTotal();
//
//                        Log.d("TOTLE LEAD Count","total"+total);
//
//
//                        open = recordCount.get(0).getOpen();
//
//                        Log.v("OPEN", String.valueOf(open));
//                        progress = recordCount.get(0).getProgress();
//                        close = recordCount.get(0).getClose();
//                        new SharedPrefClass(requireActivity().getApplicationContext()).totalLeadDetails(total,open,progress,close);
//
//                        arrayList = new ArrayList<>();
//                        arrayList.add(new DataModel("Total Leads", new SharedPrefClass(requireActivity().getApplicationContext()).getLotal_lead(),R.drawable.img_total, "#00c0ef"));
//                        // arrayList.add(new DataModel("Total Leads", 500,R.drawable.img_total, "#00c0ef"));
//                        arrayList.add(new DataModel("Lead Open", new SharedPrefClass(requireActivity().getApplicationContext()).getOpen_lead(),R.drawable.lead_status_open, "#00a65a"));
//
//                        Log.v("Lead Open", String.valueOf(new SharedPrefClass(requireActivity().getApplicationContext()).getOpen_lead()));
//                        arrayList.add(new DataModel("Lead In Process",new SharedPrefClass(requireActivity().getApplicationContext()).getProgress_lead(), R.drawable.lead_status_pending, "#f39c12"));
//                        arrayList.add(new DataModel("Lead Closed",new SharedPrefClass(requireActivity().getApplicationContext()).getClose_lead(), R.drawable.lead_status_close, "#dd4b39"));
//
//                        DashboardAdapter adapter = new DashboardAdapter(requireActivity().getApplicationContext(), arrayList, HomeFragment.this);
//                       // recyclerView.setAdapter(adapter);
//// set a GridLayoutManager with default vertical orientation and 3 number of columns
//
////                 start();
//                    }catch (NullPointerException npe)
//                    {
//                        Log.e(TAG, "Error : "+npe, npe);
//                    }
//
//                }
//            }
//
//            @Override
//            public void onFailure(Call<LeadCountModel> call, Throwable t) {
//
//            }
//        });
//    }
//
//    public void callLeadTodayApi(String key){
//
//        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
//
//
//        Log.v("key","key"+key);
//        apiInterface.getLeadTotal(key).enqueue(new Callback<TotalCountModel>() {
//            @Override
//            public void onResponse(Call<TotalCountModel> call, Response<TotalCountModel> response) {
//                if (response.isSuccessful()) {
//                    try {
//                        //        new DialogClass(HomeActivity.this).stopProgress();
//                        dialogClass.stopProgress();
//
//                        List<Today_count> today_counts = response.body().getToday_count();
//                        TodaySta = new ArrayList<>();
//                        TodaySta.add(new DataModelStatus("TOTAL", today_counts.get(0).getTodayTotal(),R.drawable.lead_status_total, "#00c0ef"));
//                        TodaySta.add(new DataModelStatus("PENDING",today_counts.get(0).getTodayPending(), R.drawable.lead_status_open, "#00a65a"));//00a65a
//
//                        TodaySta.add(new DataModelStatus("TODAY ACTIVITY", today_counts.get(0).getTodayOpen(),R.drawable.lead_status_pending, "#f39c12"));
//                        //   TodaySta.add(new DataModelStatus("Open", 100,R.drawable.lead_status_open, "#00a65a"));
//                        TodaySta.add(new DataModelStatus("CLOSED",today_counts.get(0).getTodayClose(), R.drawable.lead_status_close, "#dd4b39"));
//                        TodayListStatusAdapter todayStaus = new TodayListStatusAdapter(requireActivity().getApplicationContext(), TodaySta, HomeFragment.this);
//                      //  recyclerTodayStatus.setAdapter(todayStaus);
//
//// set a GridLayoutManager with default vertical orientation and 3 number of columns
//
////                    start();
//                    }catch (NullPointerException npe)
//                    {
//                        Log.e(TAG, "Error : "+npe, npe);
//                    }
//
//                }
//            }
//
//            @Override
//            public void onFailure(Call<TotalCountModel> call, Throwable t) {
//
//            }
//        });
//
//
//    }
//
//    @Override
//    public void onResume() {
//        super.onResume();
//        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
//      //  callLeadCountApi(key1);
//    }
//
//    @Override
//    public void onClick(View v) {
//        switch (v.getId())
//        {
//         /*   case R.id.layoutTotal:
//            {
//                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
//                new SharedPrefClass(HomeActivity.this).setLeadType(3);
//                break;
//            }*/
//          /*  case R.id.layoutOpen:
//            {
//                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
//                new SharedPrefClass(HomeActivity.this).setLeadType(1);
//                break;
//            }
//            case R.id.layoutInProgress:
//            {
//                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
//                new SharedPrefClass(HomeActivity.this).setLeadType(4);
//                break;
//            }
//            case R.id.layoutClose:
//            {
//                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
//                new SharedPrefClass(HomeActivity.this).setLeadType(2);
//                break;
//            }*/
//            default:
//            {
//                Toast.makeText(requireActivity().getApplicationContext(), "Nothing Found", Toast.LENGTH_LONG).show();
//            }
//        }
//    }
//
//    public void setTotalProgress(int i) {
//        totalPBar.setMax(i);
//        totalPBar.setProgress(i);
//        new Simple().setTotal(i);
//    }
//
//    public void setOpenProgress(int i) {
//        try {
//            int j = i*100;
//            int totalVal = Integer.parseInt(totalTV.getText().toString());
//            int k = 0;
//            k = (j) / totalVal;
//            openPBar.setMax(j);
//            openPBar.setProgress(k);
//        }catch (ArithmeticException ae)
//        {
//            Log.e(TAG, "Error : "+ae, ae);
//        }
//    }
//    public void setInProgress(int i) {
//        try {
//            int j = i*100;
//            int totalVal = Integer.parseInt(totalTV.getText().toString());
//            int k = 0;
//            k = (j) / totalVal;
//            inProgressBar.setMax(j);
//            inProgressBar.setProgress(k);
//        }catch (ArithmeticException ae)
//        {
//            Log.e(TAG, "Error : "+ae, ae);
//        }
//    }
//    public void setCloseProgress(int i) {
//        try {
//            int j = i*100;
//            int totalVal = Integer.parseInt(totalTV.getText().toString());
//            int k = 0;
//            k = (j) / totalVal;
//            closePBar.setMax(j);
//            closePBar.setProgress(k);
//        }catch (ArithmeticException ae)
//        {
//            Log.e(TAG, "Error : "+ae, ae);
//        }
//    }
//
//    @Override
//    public void onItemClick(DataModel item) {
//
//        Log.d("IEMS_data",item.toString());
//
//
//        if(item.text.equals("Total Leads"))
//        {
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(3);
//
//            //
//
//        }
//        else if(item.text.equals("Lead Open")) {
//
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(1);
//
//        }
//        else if(item.text.equals("Lead In Process")) {
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(4);
//
//        }
//
//        else if(item.text.equals("Lead Closed"))
//        {
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(2);
//        }
//    }
//    private void callLeadTotal(String key1) {
//
//        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
//        apiInterface.getLeadList(key1, 3).enqueue(new Callback<LeadListModel>() {
//            @Override
//            public void onResponse(Call<LeadListModel> call, Response<LeadListModel> response) {
//
//                if(response.isSuccessful()){
//                    try {
//                        dialogClass.stopProgress();
//                        List<LeadList> recordLeadList = response.body().getLeadList();
//                        for (int i=0; i<recordLeadList.size(); i++) {
//
//                            if(recordLeadList.get(1).getId() != null)
//                            {
//
//                                Log.v("lead_id","lead_id"+recordLeadList.get(1).getId());
//
//                                //    new SharedPrefClass(HomeActivity.this).totalLeadID(Integer.parseInt(recordLeadList.get(1).getId()));
//
//                            }
//                        }
//                    }catch (NullPointerException npe)
//                    {
//                    }
//
//                }else{
//                }
//            }
//
//            @Override
//            public void onFailure(Call<LeadListModel> call, Throwable t) {
//
//            }
//        });
//
//
//    }
//
//    @Override
//    public void onItemClick(DataModelStatus item) {
//
//        if(item.text.equals("TOTAL"))
//        {
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(6);
//
//        }
//        else if(item.text.equals("PENDING")) {
//
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(8);
//
//        }
//        else if(item.text.equals("TODAY ACTIVITY")) {
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(7);
//        }
//        else if(item.text.equals("CLOSED"))
//        {
//            Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
//            new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(9);
//
//        }
//
//    }
//
//    public void onBackPressed() {
//
//        AlertDialog.Builder BackAlertDialog = new AlertDialog.Builder(requireActivity().getApplicationContext());
//        BackAlertDialog.setTitle("Pofpfin Solutions");
//        BackAlertDialog.setMessage("Are you sure want to exit ?");
//        BackAlertDialog.setNegativeButton("NO",
//                new DialogInterface.OnClickListener() {
//                    public void onClick(DialogInterface dialog, int which) {
//                        dialog.cancel();
//                    }
//                });
//        BackAlertDialog.setPositiveButton("Yes",
//                new DialogInterface.OnClickListener() {
//                    public void onClick(DialogInterface dialog, int which) {
//                        requireActivity().finish();
//                    }
//                });
//
//        BackAlertDialog.show();
//    }

}