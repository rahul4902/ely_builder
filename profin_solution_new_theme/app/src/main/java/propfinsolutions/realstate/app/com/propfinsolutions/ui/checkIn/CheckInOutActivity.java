package propfinsolutions.realstate.app.com.propfinsolutions.ui.checkIn;

import android.content.SharedPreferences;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.location.Address;
import android.location.Geocoder;
import android.location.Location;
import android.location.LocationManager;
import android.location.LocationRequest;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.activity.EdgeToEdge;
import androidx.activity.OnBackPressedCallback;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.core.graphics.Insets;
import androidx.core.view.ViewCompat;
import androidx.core.view.WindowInsetsCompat;

import com.google.android.gms.common.api.GoogleApiClient;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.model.LatLng;

import java.io.IOException;
import java.text.SimpleDateFormat;
import java.util.ArrayList;
import java.util.Date;
import java.util.List;
import java.util.Locale;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.checkInOut.CheckInOutModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.FollowUpActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.HomeActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Session;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CheckInOutActivity extends AppCompatActivity implements View.OnClickListener{
    ImageView back_image;
    LocationManager locationManager1;
    //Context context;
    private static final String TAG = CheckInOutActivity.class.getSimpleName();
    TextView tvcheckin, tvcheckOut;
    public final static int TAG_PERMISSION_CODE = 1;

    boolean isGPSEnable = false;
    String INorOUT;
    public static String address = "";
    private static final int REQUEST_PERMISSIONS_REQUEST_CODE = 34;
    /**
     * The desired interval for location updates. Inexact. Updates may be more or less frequent.
     */
    private static final long UPDATE_INTERVAL = 6000; // Every 60 seconds.
    private Button mRequestUpdatesButton;
    /**
     * The fastest rate for active location updates. Updates will never be more frequent
     * than this value, but they may be less frequent.
     */
    private static final long FASTEST_UPDATE_INTERVAL = 3000; // Every 30 seconds

    /**
     * The max time before batched results are delivered by location services. Results may be
     * delivered sooner than this interval.
     */
    private static final long MAX_WAIT_TIME = UPDATE_INTERVAL * 5; // Every 5 minutes.

    /**
     * Stores parameters for requests to the FusedLocationProviderApi.
     */
    private LocationRequest mLocationRequest;
    TextView tvversionCode;
    /**
     * Provides access to the Fused Location Provider API.
     */
    private FusedLocationProviderClient mFusedLocationClient;
    EditText edUserRemark;
    DialogClass dialogClass;
    boolean isNetworkEnable = false;
    LocationManager locationManager;
    Location location;
    private static final int PLAY_SERVICES_RESOLUTION_REQUEST = 9000;
    private ArrayList<String> permissionsToRequest;
    private ArrayList<String> permissionsRejected = new ArrayList<>();
    private ArrayList<String> permissions = new ArrayList<>();
    public static final int MY_PERMISSIONS_REQUEST_LOCATION = 99;
    private final static int ALL_PERMISSIONS_RESULT = 101;
    Double latitude, lagitute;
    String check_in_out_value;
    SharedPreferences shared;
    GoogleApiClient mGoogleApiClient;
    Session session;
    TextView button;
    Button but;
    private long lastClickTime = 0;
    private static final long DEBOUNCE_DELAY = 500;
    public static final String MyPREFERENCES = "MyPrefs";
    public static final String Name = "nameKey";
    public static final String Phone = "phoneKey";
    public static final String Email = "emailKey";
    String current_loc;

    SharedPreferences sharedpreferences;

    private static final int LOCATION_PERMISSION_REQUEST_CODE = 100;
    private FusedLocationProviderClient fusedLocationProviderClient;
    private LatLng startLatLng;
    String date;
    private String key1;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);

        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen

        setContentView(R.layout.activity_check_in_out);

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        tvcheckin = findViewById(R.id.tvcheckin);
        tvcheckOut = findViewById(R.id.tvcheckOut);
        edUserRemark = findViewById(R.id.edUserRemark);
        dialogClass = DialogClass.getInstance();

        fusedLocationProviderClient = LocationServices.getFusedLocationProviderClient(this);
        date = new SimpleDateFormat("yyyy-MM-dd").format(new Date());

        String checkStatus = getIntent().getStringExtra("checkData");
        Log.d("CheckInOutActivity", "Received checkStatus: " + checkStatus);
        // Optionally retrieve num if required

        key1 = new SharedPrefClass(this).getSimpleKey();
        Log.v("key","key1 : "+key1);

        if (getSupportActionBar() != null) {
            getSupportActionBar().setDisplayHomeAsUpEnabled(true);
            getSupportActionBar().setDisplayShowHomeEnabled(true);
            getSupportActionBar().setDisplayShowTitleEnabled(false);

            // Set the default back arrow with a white tint
            Drawable upArrow = ContextCompat.getDrawable(this, R.drawable.abc_ic_ab_back_material); // Default back arrow drawable
            if (upArrow != null) {
                upArrow.setColorFilter(ContextCompat.getColor(this, R.color.white), PorterDuff.Mode.SRC_ATOP); // Tint to white
                getSupportActionBar().setHomeAsUpIndicator(upArrow); // Set the tinted arrow
            }
        }

        TextView toolbarTitle = toolbar.findViewById(R.id.toolbar_title);
        if (toolbarTitle != null) {
            toolbarTitle.setText("CheckIn/Out"); // Set custom title
        }
        // Handle the back arrow click
        toolbar.setNavigationOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                getOnBackPressedDispatcher().onBackPressed();
            }
        });
        // Optionally, register a back press callback
        getOnBackPressedDispatcher().addCallback(this, new OnBackPressedCallback(true) {
            @Override
            public void handleOnBackPressed() {
                finish();
            }
        });

        if (checkStatus != null && checkStatus.equals("checkIn")) {
            tvcheckin.setVisibility(View.VISIBLE);
            tvcheckOut.setVisibility(View.GONE);
        } else {
            tvcheckOut.setVisibility(View.VISIBLE);
            tvcheckin.setVisibility(View.GONE);
        }

        tvcheckin.setOnClickListener(this);
        tvcheckOut.setOnClickListener(this);
    }

    public void callCheckIn(String key, double lat, double lng, String type, String check_in_rem, String check_in_address, String check_in_date) {
        dialogClass.startProgress(this);
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.employee_checkin(key, lat, lng, type, check_in_rem, check_in_address, check_in_date).enqueue(new Callback<CheckInOutModel>() {
            @Override
            public void onResponse(Call<CheckInOutModel> call, Response<CheckInOutModel> response) {
                String status = response.body().getStatus();
                if (status.equals("success")){
                    new SharedPrefClass(CheckInOutActivity.this).setCheckInDate(check_in_date);
                    Log.d("SuccessMessage", response.body().getData().getMsg());
                    Toast.makeText(CheckInOutActivity.this, response.body().getData().getMsg(), Toast.LENGTH_SHORT).show();
                    finish();
                }else {
                    Log.d("failed", "failed.");
                  //  Toast.makeText(CheckInOutActivity.this, response.body().getData().getMsg(), Toast.LENGTH_SHORT).show();
                }
                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<CheckInOutModel> call, Throwable t) {
                dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
                Log.d("onFailure", "failed : "+ t.getMessage());
            }
        });
    }

    public void callCheckOut(String key, double lat, double lng, String type, String check_out_rem, String check_out_address, String check_in_date, String check_out_date) {
        dialogClass.startProgress(this);
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.employee_checkout(key, lat, lng, type, check_out_rem, check_out_address, check_in_date, check_out_date).enqueue(new Callback<CheckInOutModel>() {
            @Override
            public void onResponse(Call<CheckInOutModel> call, Response<CheckInOutModel> response) {
                String status = response.body().getStatus();
                if (status.equals("success")){
                    Log.d("SuccessMessage", response.body().getData().getMsg());
                    Toast.makeText(CheckInOutActivity.this, response.body().getData().getMsg(), Toast.LENGTH_SHORT).show();
                    finish();
                }else {
                    Log.d("failed", "response.body().getData().getMsg()");
                }
                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<CheckInOutModel> call, Throwable t) {
                dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
                Log.d("onFailure", "failed : "+ t.getMessage());
            }
        });
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()){
            case R.id.tvcheckin:
                getCurrentLocationForCheckIn();
                break;
            case R.id.tvcheckOut:
                getCurrentLocationForCheckOut();
                break;
        }
    }

    private void getCurrentLocationForCheckIn() {
        try {
            fusedLocationProviderClient.getLastLocation().addOnSuccessListener(this, location -> {
                if (location != null) {
                    startLatLng = new LatLng(location.getLatitude(), location.getLongitude());

//                    saveStartLocation(startLatLng);
                    Log.d(TAG, "check in position : " + startLatLng);

                    // Use Geocoder to get address from LatLng
                    Geocoder geocoder = new Geocoder(this, Locale.getDefault());
                    try {
                        List<Address> addresses = geocoder.getFromLocation(
                                location.getLatitude(),
                                location.getLongitude(),
                                5 // Get up to 5 addresses
                        );

                        if (addresses != null && addresses.size() > 1) {
                            Address secondAddress = addresses.get(1);

                            // Check if the second line exists
                            String addressText = secondAddress.getAddressLine(1);
                            if (addressText == null) {
                                // Fall back to the first line if the second line is null
                                addressText = secondAddress.getAddressLine(0);
                            }

                            // Log the address and set it in the TextView
                            Log.d(TAG, "Address: " + addressText);

                            callCheckIn(key1,  location.getLatitude(), location.getLongitude(), "checkin",  edUserRemark.getText().toString().trim(), addressText.trim(), date);

//                            checkInAddress.setText(addressText); // Set the selected address in TextView
                        } else {
//                            checkInAddress.setText("Address not found");
                            Log.d(TAG, "No second address available.");
                        }
                    } catch (IOException e) {
                        e.printStackTrace();
//                        checkInAddress.setText("Unable to fetch address");
                        Log.e(TAG, "Geocoder failed", e);
                    }
                }
            });
        } catch (SecurityException e) {
            Log.e(TAG, "Location permission not granted", e);
        }
    }
    private void getCurrentLocationForCheckOut() {
        try {
            fusedLocationProviderClient.getLastLocation().addOnSuccessListener(this, location -> {
                if (location != null) {
                    startLatLng = new LatLng(location.getLatitude(), location.getLongitude());

//                    saveStartLocation(startLatLng);
                    Log.d(TAG, "check in position : " + startLatLng);

                    // Use Geocoder to get address from LatLng
                    Geocoder geocoder = new Geocoder(this, Locale.getDefault());
                    try {
                        List<Address> addresses = geocoder.getFromLocation(
                                location.getLatitude(),
                                location.getLongitude(),
                                5 // Get up to 5 addresses
                        );

                        if (addresses != null && addresses.size() > 1) {
                            Address secondAddress = addresses.get(1);

                            // Check if the second line exists
                            String addressText = secondAddress.getAddressLine(1);
                            if (addressText == null) {
                                // Fall back to the first line if the second line is null
                                addressText = secondAddress.getAddressLine(0);
                            }

                            // Log the address and set it in the TextView
                            Log.d(TAG, "Address: " + addressText);

                            String checkInDate =  new SharedPrefClass(CheckInOutActivity.this).getCheckInDate();

                            callCheckOut(key1,  location.getLatitude(), location.getLongitude(), "checkout",  edUserRemark.getText().toString().trim(), addressText, checkInDate,date);

//                            checkInAddress.setText(addressText); // Set the selected address in TextView
                        } else {
//                            checkInAddress.setText("Address not found");
                            Log.d(TAG, "No second address available.");
                        }
                    } catch (IOException e) {
                        e.printStackTrace();
//                        checkInAddress.setText("Unable to fetch address");
                        Log.e(TAG, "Geocoder failed", e);
                    }
                }
            });
        } catch (SecurityException e) {
            Log.e(TAG, "Location permission not granted", e);
        }
    }
}