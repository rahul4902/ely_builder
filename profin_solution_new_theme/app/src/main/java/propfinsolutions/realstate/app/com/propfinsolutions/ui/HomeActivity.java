package propfinsolutions.realstate.app.com.propfinsolutions.ui;
import android.animation.Animator;
import android.animation.AnimatorListenerAdapter;
import android.animation.ObjectAnimator;
import android.animation.ValueAnimator;
import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.Window;
import android.view.WindowManager;
import android.view.animation.AccelerateDecelerateInterpolator;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.core.view.MenuItemCompat;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import com.google.android.material.bottomnavigation.BottomNavigationView;
import com.google.firebase.messaging.FirebaseMessaging;

import java.time.LocalDate;
import java.util.ArrayList;
import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.NotificationAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.DashboardAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.TodayListStatusAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModelStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadList;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.Simple;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationRead;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.ForegroundService;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.CallLogsFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.calendar.CalendarFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.HomeFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.NotificationFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.ProfileFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;
    public class HomeActivity extends AppCompatActivity implements View.OnClickListener,DashboardAdapter.ItemListener,TodayListStatusAdapter.ItemListener{
    private static final String TAG = HomeActivity.class.getSimpleName();
    Toolbar mToolbar;
    ProgressBar totalPBar, openPBar, inProgressBar, closePBar ;
    TextView mToolbarTV, totalTV, openTV, inProgressTV, closeTV;
    Handler progressHandler = new Handler();
    int notifyCount = 0;
    int i = 0;
    String[] nameArray = {"New Lead", "Leads", "Open Lead", "Closed Lead", "Log Out"};
    //String[] nameArray = {"Aestro", "Blender", "Cupcake", "Donut", "Eclair", "Froyo", "GingerBread", "HoneyComb", "IceCream Sandwich", "JelliBean", "KitKat","Aestro", "Blender", "Cupcake", "Donut", "Eclair", "Froyo", "GingerBread", "HoneyComb", "IceCream Sandwich", "JelliBean", "KitKat", "Lollipop", "MarshMallow"};
    LinearLayout totalLayout, openLayout, inProgressLayout, closelayout;
    int total , open , progress , close ;
    DialogClass dialogClass;
    private TextView userName;
    private String key1;
    RecyclerView recyclerView,recyclerTodayStatus;
    ArrayList<DataModel> arrayList;
    ArrayList<DataModelStatus> TodaySta;
    private BottomNavigationView bottomNavigationView;
    ImageView notificationIcon, logout;
    TextView notificationBadge;
    View actionView;
        @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        requestWindowFeature(Window.FEATURE_NO_TITLE);

        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen

        setContentView(R.layout.activity_home);

        mToolbar = findViewById(R.id.toolbar);
        setSupportActionBar(mToolbar);

        // Hide the default title if needed
        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayShowTitleEnabled(false);
        }

        bottomNavigationView = findViewById(R.id.bottom_navigation);
        replacementFragment(new HomeFragment(), "");
        bottomNavigationView.setOnItemSelectedListener(item -> {
            switch (item.getItemId()){
                case R.id.home:
                    replacementFragment(new HomeFragment(), "");
                    break;
                case R.id.calendar:
                    replacementFragment(new CalendarFragment(), "Calendar");
                    break;
                case R.id.callLog:
                    replacementFragment(new CallLogsFragment(), "Call Logs");
                    break;
                case R.id.profile:
                    replacementFragment(new ProfileFragment(), "Profile");
                    break;
            }
            return true;

        });

//        mToolbar = (Toolbar)findViewById(R.id.toolbar);
//        mToolbarTV = (TextView)findViewById(R.id.toolbar_title);
//

        Intent intent=getIntent();
        String leadId=intent.getStringExtra("leadId");
        Log.v("LEADIDTEST","LEADIDTEST"+leadId);
        if(leadId!=null){
        Toast.makeText(getApplicationContext(),leadId,Toast.LENGTH_LONG).show();
         }

        FirebaseMessaging.getInstance().subscribeToTopic("leadId");

        key1 = new SharedPrefClass(this).getSimpleKey();
        Log.v("key","key1 : "+key1);

        callUserNotifyCountApi(key1);

//        totalPBar = (ProgressBar) findViewById(R.id.pbTotal);
//        openPBar = (ProgressBar) findViewById(R.id.pbOpen);
//        inProgressBar = (ProgressBar) findViewById(R.id.pbInProgress);
//        closePBar = (ProgressBar) findViewById(R.id.pbClose);
//        totalTV = (TextView) findViewById(R.id.tvTotal);
//        openTV = (TextView) findViewById(R.id.tvOpen);
//        inProgressTV = (TextView) findViewById(R.id.tvInProgress);
//        closeTV = (TextView) findViewById(R.id.tvClose);
//        userName=(TextView)findViewById(R.id.userName);
      ///  totalLayout = (LinearLayout)findViewById(R.id.layoutTotal);
        /*openLayout = (LinearLayout)findViewById(R.id.layoutOpen);
        inProgressLayout = (LinearLayout)findViewById(R.id.layoutInProgress);
        closelayout = (LinearLayout)findViewById(R.id.layoutClose);*/
//        String UserName = new SharedPrefClass(HomeActivity.this).getUserName().toString();
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
//        dialogClass.startProgress(HomeActivity.this);
//        key1 = new SharedPrefClass(HomeActivity.this).getSimpleKey();
//        Log.v("key","key1"+key1);
//        callLeadCountApi(key1);
//        callLeadTodayApi(key1);

     //   callLeadTotal(key1);
    //    callLeadTotalLeadCount(key1);
          startService();
    }

//        @Override
//        public boolean onCreateOptionsMenu(Menu menu) {
//            getMenuInflater().inflate(R.menu.toolbar_menu, menu);
//            return true;
//        }

        @Override
        public boolean onCreateOptionsMenu(Menu menu) {
            getMenuInflater().inflate(R.menu.toolbar_menu, menu);

            // Find the notification menu item
            MenuItem menuItem = menu.findItem(R.id.action_notification);
            // Inflate the custom view for the notification item
            MenuItemCompat.setActionView(menuItem, R.layout.menu_item_with_badage);

            // Access the action view for updating the counter
             actionView = MenuItemCompat.getActionView(menuItem);
             notificationIcon = actionView.findViewById(R.id.notification_icon);
             notificationBadge = actionView.findViewById(R.id.notification_badge);

            // Initially hide the badge if no unread notifications
            notificationBadge.setVisibility(View.GONE);

            // Add click listener to the notification icon
            notificationIcon.setOnClickListener(v -> {
                updateNotificationBadge(notificationBadge, 0);
                callReadNotifyApi(key1);
                replacementFragment(new NotificationFragment(), "Notification");
                // Reset unread notifications when clicked

                // Start bell ring animation
                ringBellAnimation(notificationIcon);
            });

            // Example to update the badge count dynamically
            // Set unread notifications count to 3

            return true;
        }

        private void updateNotificationBadge(TextView notificationBadge, int unreadCount) {
            if (unreadCount > 0) {
                notificationBadge.setText(String.valueOf(unreadCount));
                notificationBadge.setVisibility(View.VISIBLE);
            } else {
                notificationBadge.setVisibility(View.GONE);
            }
        }

        @Override
        public boolean onOptionsItemSelected(MenuItem item) {
            switch (item.getItemId()) {
                case R.id.action_notification:
                    // Handle notification action if needed (optional)
                    return true;

                case R.id.action_logout:
                    // Handle the logout action here
                    logoutUser();
                    return true;

                default:
                    return super.onOptionsItemSelected(item);
            }

//            if (item.getItemId() == R.id.action_notification) {
//                // Handle notification icon click - replace with NotificationFragment
//                callReadNotifyApi(key1);
//                replacementFragment(new NotificationFragment());
//                return true;
//            }
        }

        // Create a global animator reference to control the animation lifecycle
        private ObjectAnimator bellAnimator;

        // Method to create a bell ring (pendulum-like) animation
        private void ringBellAnimation(ImageView notificationIcon) {
            // Cancel any ongoing animation to ensure it starts fresh each time
            if (bellAnimator != null && bellAnimator.isRunning()) {
                bellAnimator.cancel();
            }

            // Reset the rotation to 0 before starting the animation
            notificationIcon.setRotation(0f);

            // Create an ObjectAnimator for pendulum-like rotation between -30 and 30 degrees
            bellAnimator = ObjectAnimator.ofFloat(notificationIcon, "rotation", -30f, 30f);
            bellAnimator.setDuration(200);  // Duration of the swing
            bellAnimator.setInterpolator(new AccelerateDecelerateInterpolator());  // Smooth acceleration and deceleration
            bellAnimator.setRepeatMode(ValueAnimator.REVERSE);  // Reverse direction when the end is reached
            bellAnimator.setRepeatCount(4);  // Number of swings

            // Add listener to reset the rotation to 0 when the animation ends
            bellAnimator.addListener(new AnimatorListenerAdapter() {
                @Override
                public void onAnimationEnd(Animator animation) {
                    // Reset the rotation to 0 after the animation finishes
                    notificationIcon.setRotation(0f);
                }
            });

            bellAnimator.start();  // Start the animation
        }



        private void replacementFragment(Fragment fragment, String title){
        FragmentManager manager = getSupportFragmentManager();
        FragmentTransaction transaction = manager.beginTransaction();
        transaction.replace(R.id.frame_container, fragment).addToBackStack(null).commit();
        // Set the toolbar title dynamically
        TextView toolbarTitle = mToolbar.findViewById(R.id.toolbar_title); // Assuming you have a TextView in your Toolbar
        if (toolbarTitle != null) {
            if (!title.isEmpty()){
                toolbarTitle.setText(title); // Set the fragment title as toolbar title
            }else {
                toolbarTitle.setText(R.string.app_name); // Set the fragment title as toolbar title
            }
        }
    }


    private void logoutUser(){
        new AlertDialog.Builder(this)
                .setTitle("Logout Confirmation")
                .setMessage("Are you sure you want to logout?")
                .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        // User clicked Yes button, perform logout
                        final SharedPreferences SpyAppData = HomeActivity.this.getSharedPreferences(HomeActivity.this.getPackageName(), 0);
                        SharedPreferences.Editor editor = SpyAppData.edit();
                        editor.clear();
                        editor.commit();

                        new SharedPrefClass(HomeActivity.this).logoutUser();
                        Intent intent = new Intent(HomeActivity.this, SplashActivity.class);
                        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_NEW_TASK);
                        startActivity(intent);
                    }
                })
                .setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
                    @Override
                    public void onClick(DialogInterface dialog, int which) {
                        // User clicked Cancel button, dismiss the dialog
                        dialog.dismiss();
                    }
                })
                .show();
    }


        public void callUserNotifyCountApi(String key) {
            ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
            Log.v("newKey", "key : " + key);
            apiInterface.getUserNotification(key).enqueue(new Callback<NotificationResponseModel>() {
                @Override
                public void onResponse(Call<NotificationResponseModel> call, Response<NotificationResponseModel> response) {
//                    dialogClass.stopProgress();
                    List<NotificationResponseModel.UserNotifications> list = response.body().getUserNotifications();
                    List<NotificationResponseModel.Message> messages = response.body().getMessage();
                    ArrayList<NotificationResponseModel.UserNotifications> notifyList = new ArrayList<>();
                    if (list != null) {
                        notifyList.addAll(list);
                    } else if (messages != null && !messages.isEmpty()) {
                        // Handle the "no record found" message
                        Log.e("HomeActivity", messages.get(0).getMsg());
                        // Optionally, display a user-friendly message in the UI
                    }

                    for (int i = 0; i < notifyList.size(); i++){
                        if (notifyList.get(i).getNRead().equals("0")){
                            notifyCount++;
                            Log.d("notifyCountloop", "notifyCount : "+ notifyCount);

                        }
                    }
                    updateNotificationBadge(notificationBadge, notifyCount);
                    Log.d("notifyCountplus", "notifyCount : "+ notifyCount);

                }
                @Override
                public void onFailure(Call<NotificationResponseModel> call, Throwable t) {
//                    dialogClass.stopProgress();
                    // Handle failure (e.g., show a Toast or log the error)
                    Log.d("onFailure", "failed : "+ t.getMessage());
                }
            });
        }

//    private void callLeadTotalLeadCount(String key1) {
//            ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
//
//            Log.v("key","key"+key1);
//            apiInterface.getLeadCount(key1).enqueue(new Callback<LeadCountModel>() {
//                @Override
//                public void onResponse(Call<LeadCountModel> call, Response<LeadCountModel> response) {
//                    if (response.isSuccessful()) {
//                        try {
//                            //        new DialogClass(HomeActivity.this).stopProgress();
//                            dialogClass.stopProgress();
//                            List<RecordCount> recordCount = response.body().getRecordCount();
//
//                            if(recordCount.get(0).getTotal() != null)
//                            {
//                              Log.v("LEAD COUNT","LEAD COUNT"+recordCount.get(0).getTotal());
//
//                            }
//
////                 start();
//                        }catch (NullPointerException npe)
//                        {
//                            Log.e(TAG, "Error : "+npe, npe);
//                        }
//
//                    }
//                }
//
//                @Override
//                public void onFailure(Call<LeadCountModel> call, Throwable t) {
//
//                }
//            });
//
//        }

     public void startService() {
            Intent serviceIntent = new Intent(this, ForegroundService.class);
            serviceIntent.putExtra("inputExtra", "Foreground Service Example in Android");

            ContextCompat.startForegroundService(this, serviceIntent);
        }

        public void callReadNotifyApi(String key) {

            ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

            Log.v("newKey", "key : " + key);
            apiInterface.getNotificationRead(key).enqueue(new Callback<NotificationRead>() {
                @Override
                public void onResponse(Call<NotificationRead> call, Response<NotificationRead> response) {
                    if (response.isSuccessful() && response.body() != null) {
                        List<NotificationRead.Read> list = response.body().getRead();
                        List<NotificationRead.Message> messages = response.body().getMessage();


                    }
                }
                @Override
                public void onFailure(Call<NotificationRead> call, Throwable t) {
                    // Handle failure (e.g., show a Toast or log the error)
                    Log.d("onFailure", "failed : "+ t.getMessage());
                }
            });
        }

        /*
    public void start() {
        startProgress(total, 1);
        startProgress(open, 2);
        startProgress(progress, 3);
        startProgress(close, 4);
    }

    public void startProgress(final int i, final int leadBox) {
                            if(leadBox == 1)
                            {
                                totalPBar.setProgress(i);
                                totalTV.setText("" + i );
                                setTotalProgress(i);
                                new Simple().setTotal(i);
                            }else if (leadBox == 2)
                            {
                                openPBar.setProgress(i);
                                openTV.setText("" + i );
                                setOpenProgress(i);
                            }else if (leadBox == 3)
                            {
                                inProgressBar.setProgress(i);
                                inProgressTV.setText("" + i );
                                setInProgress(i);
                            }else if (leadBox == 4)
                            {
                                closePBar.setProgress(i);
                                closeTV.setText("" + i );
                                setCloseProgress(i);
                            }else {
                                Toast.makeText(HomeActivity.this, "Something went wrong", Toast.LENGTH_LONG).show();
                            }
                            //progressingTextView.setText("" + i + " %");
                        }

         */

                        /*
     public void callLeadCountApi(String key){

     ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

    Log.v("key","key"+key);
    apiInterface.getLeadCount(key).enqueue(new Callback<LeadCountModel>() {
        @Override
        public void onResponse(Call<LeadCountModel> call, Response<LeadCountModel> response) {
            if (response.isSuccessful()) {
                try {
                    //        new DialogClass(HomeActivity.this).stopProgress();
                    dialogClass.stopProgress();
                    List<RecordCount> recordCount = response.body().getRecordCount();

                    Log.d("TOTLE LEAD Count","total"+recordCount);

                    total = recordCount.get(0).getTotal();

                    Log.d("TOTLE LEAD Count","total"+total);


                    open = recordCount.get(0).getOpen();

                    Log.v("OPEN", String.valueOf(open));
                    progress = recordCount.get(0).getProgress();
                    close = recordCount.get(0).getClose();
                    new SharedPrefClass(HomeActivity.this).totalLeadDetails(total,open,progress,close);

                    recyclerView = (RecyclerView) findViewById(R.id.recyclerView);
                    arrayList = new ArrayList<>();
                    arrayList.add(new DataModel("Total Leads", new SharedPrefClass(HomeActivity.this).getLotal_lead(),R.drawable.img_total, "#00c0ef"));
                    // arrayList.add(new DataModel("Total Leads", 500,R.drawable.img_total, "#00c0ef"));
                    arrayList.add(new DataModel("Lead Open", new SharedPrefClass(HomeActivity.this).getOpen_lead(),R.drawable.lead_status_open, "#00a65a"));

                    Log.v("Lead Open", String.valueOf(new SharedPrefClass(HomeActivity.this).getOpen_lead()));
                    arrayList.add(new DataModel("Lead In Process",new SharedPrefClass(HomeActivity.this).getProgress_lead(), R.drawable.lead_status_pending, "#f39c12"));
                    arrayList.add(new DataModel("Lead Closed",new SharedPrefClass(HomeActivity.this).getClose_lead(), R.drawable.lead_status_close, "#dd4b39"));
                    DashboardAdapter adapter = new DashboardAdapter(HomeActivity.this, arrayList, HomeActivity.this);
                    recyclerView.setAdapter(adapter);
                    AutoFitGridLayoutManager layoutManager = new AutoFitGridLayoutManager(HomeActivity.this, 500);
                    recyclerView.setLayoutManager(layoutManager);
                    GridLayoutManager manager = new GridLayoutManager(HomeActivity.this, 2, GridLayoutManager.VERTICAL, false);
                    recyclerView.setLayoutManager(manager);

//                 start();
                }catch (NullPointerException npe)
                {
                    Log.e(TAG, "Error : "+npe, npe);
                }

            }
        }
        @Override
        public void onFailure(Call<LeadCountModel> call, Throwable t) {
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
                        dialogClass.stopProgress();
                        List<Today_count> today_counts = response.body().getToday_count();
                        recyclerTodayStatus = (RecyclerView) findViewById(R.id.recyclerTodayStatus);
                        TodaySta = new ArrayList<>();
                        TodaySta.add(new DataModelStatus("TOTAL", today_counts.get(0).getTodayTotal(),R.drawable.lead_status_total, "#00c0ef"));
                        TodaySta.add(new DataModelStatus("PENDING",today_counts.get(0).getTodayPending(), R.drawable.lead_status_open, "#00a65a"));//00a65a

                        TodaySta.add(new DataModelStatus("TODAY ACTIVITY", today_counts.get(0).getTodayOpen(),R.drawable.lead_status_pending, "#f39c12"));
                       //   TodaySta.add(new DataModelStatus("Open", 100,R.drawable.lead_status_open, "#00a65a"));
                        TodaySta.add(new DataModelStatus("CLOSED",today_counts.get(0).getTodayClose(), R.drawable.lead_status_close, "#dd4b39"));
                        TodayListStatusAdapter todayStaus = new TodayListStatusAdapter(HomeActivity.this, TodaySta, HomeActivity.this);
                        recyclerTodayStatus.setAdapter(todayStaus);
                        AutoFitGridLayoutManager layoutManagerStatus = new AutoFitGridLayoutManager(HomeActivity.this, 500);
                        recyclerTodayStatus.setLayoutManager(layoutManagerStatus);
                        GridLayoutManager managerStatus = new GridLayoutManager(HomeActivity.this, 4, GridLayoutManager.VERTICAL, false);
                        recyclerTodayStatus.setLayoutManager(managerStatus);
//                    start();
                    }catch (NullPointerException npe)
                    {
                        Log.e(TAG, "Error : "+npe, npe);
                    }
                }
            }

            @Override
            public void onFailure(Call<TotalCountModel> call, Throwable t) {

            }
        });


    }

                         */

    @Override
    protected void onResume() {
        super.onResume();
      //  key1 = new SharedPrefClass(HomeActivity.this).getSimpleKey();
     //   callLeadCountApi(key1);
       // callUserNotifyCountApi(key1);
    }


    @Override
    public void onClick(View v) {
        switch (v.getId())
        {
         /*   case R.id.layoutTotal:
            {
                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
                new SharedPrefClass(HomeActivity.this).setLeadType(3);
                break;
            }*/
          /*  case R.id.layoutOpen:
            {
                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
                new SharedPrefClass(HomeActivity.this).setLeadType(1);
                break;
            }
            case R.id.layoutInProgress:
            {
                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
                new SharedPrefClass(HomeActivity.this).setLeadType(4);
                break;
            }
            case R.id.layoutClose:
            {
                Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
                new SharedPrefClass(HomeActivity.this).setLeadType(2);
                break;
            }*/
            default:
            {
                Toast.makeText(HomeActivity.this, "Nothing Found", Toast.LENGTH_LONG).show();
            }
        }
    }

    public void setTotalProgress(int i) {
        totalPBar.setMax(i);
        totalPBar.setProgress(i);
        new Simple().setTotal(i);
    }

    public void setOpenProgress(int i) {
        try {
            int j = i*100;
            int totalVal = Integer.parseInt(totalTV.getText().toString());
            int k = 0;
            k = (j) / totalVal;
            openPBar.setMax(j);
            openPBar.setProgress(k);
        }catch (ArithmeticException ae)
        {
            Log.e(TAG, "Error : "+ae, ae);
        }
    }

    public void setInProgress(int i) {
        try {
            int j = i*100;
            int totalVal = Integer.parseInt(totalTV.getText().toString());
            int k = 0;
            k = (j) / totalVal;
            inProgressBar.setMax(j);
            inProgressBar.setProgress(k);
        }catch (ArithmeticException ae)
        {
            Log.e(TAG, "Error : "+ae, ae);
        }
    }

    public void setCloseProgress(int i) {
        try {
            int j = i*100;
            int totalVal = Integer.parseInt(totalTV.getText().toString());
            int k = 0;
            k = (j) / totalVal;
            closePBar.setMax(j);
            closePBar.setProgress(k);
        }catch (ArithmeticException ae)
        {
            Log.e(TAG, "Error : "+ae, ae);
        }
    }

    @Override
    public void onItemClick(DataModel item) {

        Log.d("IEMS_data",item.toString());


        if(item.text.equals("Total Leads"))
        {
            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(3);

            //

            }
        else if(item.text.equals("Lead Open")) {

            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(1);

            }
        else if(item.text.equals("Lead In Process")) {
            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(4);

            }

            else if(item.text.equals("Lead Closed"))
            {
            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(2);
            }
         }

    private void callLeadTotal(String key1) {

            ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
            apiInterface.getLeadList(key1, 3).enqueue(new Callback<LeadListModel>() {
                @Override
                public void onResponse(Call<LeadListModel> call, Response<LeadListModel> response) {

                    if(response.isSuccessful()){
                        try {
                            dialogClass.stopProgress();
                            List<LeadList> recordLeadList = response.body().getLeadList();
                            for (int i=0; i<recordLeadList.size(); i++) {

                                if(recordLeadList.get(1).getId() != null)
                                {

                                    Log.v("lead_id","lead_id"+recordLeadList.get(1).getId());

                                //    new SharedPrefClass(HomeActivity.this).totalLeadID(Integer.parseInt(recordLeadList.get(1).getId()));

                                }
                            }
                        }catch (NullPointerException npe)
                        {
                        }

                    }else{
                    }
                }

                @Override
                public void onFailure(Call<LeadListModel> call, Throwable t) {

                }
            });


        }
        @Override
    public void onItemClick(DataModelStatus item) {

        if(item.text.equals("TOTAL"))
        {
            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(6);
        }
        else if(item.text.equals("PENDING")) {

            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(8);
        }
        else if(item.text.equals("TODAY ACTIVITY")) {
            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(7);
        }
        else if(item.text.equals("CLOSED"))
        {
            Navigatior.getClassInstance().navigateToActivity(HomeActivity.this, LeadListActivity.class);
            new SharedPrefClass(HomeActivity.this).setLeadType(9);
        }

    }
    public void onBackPressed() {

       // super.onBackPressed();
        AlertDialog.Builder BackAlertDialog = new AlertDialog.Builder(HomeActivity.this);
        BackAlertDialog.setTitle("Pofpfin Solutions");
        BackAlertDialog.setMessage("Are you sure want to exit ?");
        BackAlertDialog.setNegativeButton("NO",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        dialog.cancel();
                    }
                });
        BackAlertDialog.setPositiveButton("Yes",
                new DialogInterface.OnClickListener() {
                    public void onClick(DialogInterface dialog, int which) {
                        finish();
                    }
                });

        BackAlertDialog.show();
    }


}
