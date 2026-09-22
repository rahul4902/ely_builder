package propfinsolutions.realstate.app.com.propfinsolutions.ui;


import android.Manifest;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.net.wifi.WifiInfo;
import android.net.wifi.WifiManager;
import android.os.Bundle;
import android.os.CountDownTimer;
import android.text.Editable;
import android.text.TextWatcher;
import android.text.format.Formatter;
import android.util.Log;
import android.view.View;
import android.view.inputmethod.InputMethodManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.TextView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
//import com.msg91.sendotpandroid.library.IPConverter;
//import com.msg91.sendotpandroid.library.SendOTPConfig;
//import com.msg91.sendotpandroid.library.SendOtpVerification;
//import com.msg91.sendotpandroid.library.Verification;
//import com.msg91.sendotpandroid.library.VerificationListener;

import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.firebase.auth.AuthResult;
import com.google.firebase.auth.FirebaseAuth;
import com.google.firebase.auth.PhoneAuthCredential;
import com.google.firebase.auth.PhoneAuthProvider;

import java.util.ArrayList;
import java.util.List;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.LoginModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.Record;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.Config;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.AppController;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.NetworkConnectivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SmsBroadcastReceiver;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class OtpVerificationActivity extends AppCompatActivity implements
        ActivityCompat.OnRequestPermissionsResultCallback /*, VerificationListener*/ {
    public  static final String ActivityId="110";

    Button btn_log_submit;
    private static final String TAG = OtpVerificationActivity.class.getSimpleName();
    TextView resend_timer;

    //private Verification mVerification;

    String phoneNumber;
    EditText otp1;
    SmsBroadcastReceiver mSmsBroadcastReceiver;
    private static final int PERMISSION_REQUEST_ID = 100;
    private final String  BROADCAST_ACTION = "android.provider.Telephony.SMS_RECEIVED";
    private IntentFilter intentFilter;
    ApiInterface apiInterface;
    String getOtpBackend;
    ProgressBar progressBar;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        getSupportActionBar().hide(); // hide the title bar
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_otp_verification);

        requestRuntimePermissions(Manifest.permission.READ_SMS, Manifest.permission.RECEIVE_SMS, Manifest.permission.SEND_SMS);
//        checkAndRequestPermissions();
        try {
            btn_log_submit = (Button)findViewById(R.id.btn_log_submit);
            otp1=(EditText)findViewById(R.id.otp1);
            resend_timer = (TextView) findViewById(R.id.progressText);
            progressBar = findViewById(R.id.progressIndicator);

//            phoneNumber = getIntent().getStringExtra(LoginActivity.INTENT_PHONENUMBER);
//            getOtpBackend = getIntent().getStringExtra(LoginActivity.SEND_OTP_CODE);

            resend_timer.setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    ResendCode();
                }
            });
            mSmsBroadcastReceiver = new SmsBroadcastReceiver();
            intentFilter = new IntentFilter();
            intentFilter.addAction(BROADCAST_ACTION);
        }catch (Exception ee){
            ee.printStackTrace();
        }

        startTimer();
        initiateVerification();


//        otp1.addTextChangedListener(new TextWatcher() {
//            public void afterTextChanged(Editable s) {
//                if (s.length() !=0) {
//
//
//
//                    OtpVerification();
//                }
//            }
//            public void beforeTextChanged(CharSequence s, int start, int count,  int after) {
//            }
//
//            public void onTextChanged(CharSequence s, int start, int before, int count) {
//            }
//        });

        btn_log_submit.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                if (!otp1.getText().toString().trim().isEmpty()){
                    if (getOtpBackend != null){
                        progressBar.setVisibility(View.VISIBLE);

                        PhoneAuthCredential phoneAuthCredential = PhoneAuthProvider.getCredential(
                                getOtpBackend, otp1.getText().toString().trim()
                        );

                        FirebaseAuth.getInstance().signInWithCredential(phoneAuthCredential)
                                .addOnCompleteListener(new OnCompleteListener<AuthResult>() {
                                    @Override
                                    public void onComplete(@NonNull Task<AuthResult> task) {
                                        progressBar.setVisibility(View.GONE);
                                        if (task.isSuccessful()){
                                            OtpVerification();
                                        }else {
                                            Toast.makeText(OtpVerificationActivity.this, "enter correct OTP", Toast.LENGTH_SHORT).show();
                                        }
                                    }
                                });

                    }else {
                        Toast.makeText(OtpVerificationActivity.this, "please check otp or connection", Toast.LENGTH_SHORT).show();
                    }

                }else {
                    Toast.makeText(OtpVerificationActivity.this, "please enter otp", Toast.LENGTH_SHORT).show();
                }
            }
        });

    }

    private boolean checkAndRequestPermissions() {
        int permissionSendMessage = ContextCompat.checkSelfPermission(this,
                Manifest.permission.SEND_SMS);
        int receiveSMS = ContextCompat.checkSelfPermission(this,
                Manifest.permission.RECEIVE_SMS);
        int readSMS = ContextCompat.checkSelfPermission(this,
                Manifest.permission.READ_SMS);

        int cameraPermission = ContextCompat.checkSelfPermission(this, android.Manifest.permission.CAMERA);
        int readPermission = ContextCompat.checkSelfPermission(this, android.Manifest.permission.READ_EXTERNAL_STORAGE);
        int writePermission = ContextCompat.checkSelfPermission(this, android.Manifest.permission.WRITE_EXTERNAL_STORAGE);
        //  int recordpermission = ContextCompat.checkSelfPermission(this, Manifest.permission.RECORD_AUDIO);

        int medeiapermission = ContextCompat.checkSelfPermission(this, Manifest.permission.MEDIA_CONTENT_CONTROL);


        final List<String> listPermissionsNeeded = new ArrayList<>();

        if (permissionSendMessage != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.SEND_SMS);
        }
        if (receiveSMS != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.RECEIVE_MMS);
        }
        if (readSMS != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.READ_SMS);
        }


        if (cameraPermission != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.CAMERA);
        }

        if (readPermission != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.READ_EXTERNAL_STORAGE);
        }

        if (writePermission != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.WRITE_EXTERNAL_STORAGE);
        }

//        if (recordpermission != PackageManager.PERMISSION_GRANTED) {
//            listPermissionsNeeded.add(Manifest.permission.RECORD_AUDIO);
//        }



        if (medeiapermission != PackageManager.PERMISSION_GRANTED) {
            listPermissionsNeeded.add(Manifest.permission.MEDIA_CONTENT_CONTROL);
        }

        if (!listPermissionsNeeded.isEmpty()) {
            ActivityCompat.requestPermissions(this,
                    listPermissionsNeeded.toArray(new String[listPermissionsNeeded.size()]),
                    1);
            return true;
        }
        return true;
    }

    private void requestRuntimePermissions(String... permissions) {
        for (String perm : permissions) {
            if (ContextCompat.checkSelfPermission(this, perm) != PackageManager.PERMISSION_GRANTED) {
                ActivityCompat.requestPermissions(this, new String[]{perm}, PERMISSION_REQUEST_ID);
            }
        }
    }
    void createVerification(String phoneNumber, boolean skipPermissionCheck, String countryCode) {
        try {
            if (!skipPermissionCheck && ContextCompat.checkSelfPermission(this, Manifest.permission.READ_SMS) ==
                    PackageManager.PERMISSION_DENIED) {
                ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.READ_SMS}, 0);
                hideProgressBar();
            } else {
                boolean withoutOtp = false;
                if (NetworkConnectivity.isConnectedMobileNetwork(getApplicationContext())) {
                    withoutOtp = true;
                }else {

                }
//               SendOTPConfig otpConfig=    SendOtpVerification
//                        .config(countryCode + phoneNumber)
//                        .context(this)
//                        .autoVerification(true)
//                        .setIp(getIp(withoutOtp))
//                        .verifyWithoutOtp(withoutOtp)
//                        .unicode(false)
//                        .httpsConnection(true)
//                        .expiry("5")
//                        .senderId("UACVNM")
//                        .otplength("6")
//                        .build();
//                mVerification = SendOtpVerification.createSmsVerification
//                        (otpConfig , this);
//                mVerification.initiate();
            }
        }catch (Exception ee){
            ee.printStackTrace();
        }

    }

    @Override
    protected void onResume() {
        super.onResume();
        registerReceiver(mSmsBroadcastReceiver, intentFilter);
        Log.e("OtpActivity","Registered receiver");

    }
    @Override
    public void onBackPressed() {
        super.onBackPressed();
    }
    @Override
    protected void onPause() {
        super.onPause();
        unregisterReceiver(mSmsBroadcastReceiver);
        Log.e("OtpActivity","Unregistered receiver");
    }

    private String getIp(boolean moibleNetwork) {
        if(moibleNetwork) {
            try {
             //   return IPConverter.getIPAddress(true);
            } catch (Exception ex) {
            }
        }else {
            WifiManager wifiMgr = (WifiManager) getApplicationContext().getSystemService(WIFI_SERVICE);
            WifiInfo wifiInfo = wifiMgr.getConnectionInfo();
            int ip = wifiInfo.getIpAddress();
            return  Formatter.formatIpAddress(ip);
        }
        return "";
    }


    public void onRequestPermissionsResult(int requestCode, String[] permissions, int[] grantResults) {
        if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {

        } else {
            if (ActivityCompat.shouldShowRequestPermissionRationale(this, permissions[0])) {
                Toast.makeText(this, "This application needs permission to read your SMS to automatically verify your "
                        + "phone, you may disable the permission once you have been verified.", Toast.LENGTH_LONG)
                        .show();
            }
//            enableInputField(true);
        }
        initiateVerificationAndSuppressPermissionCheck();
    }

    void initiateVerification() {
        initiateVerification(false);
    }

    void initiateVerificationAndSuppressPermissionCheck() {
        initiateVerification(true);
    }

    void initiateVerification(boolean skipPermissionCheck) {
        Intent intent = getIntent();
        if (intent != null) {
            phoneNumber = intent.getStringExtra(LoginActivity.INTENT_PHONENUMBER);
            getOtpBackend = intent.getStringExtra(LoginActivity.SEND_OTP_CODE);
            TextView phoneText = (TextView) findViewById(R.id.mobil_number);
            phoneText.setText("+91" + phoneNumber);

            createVerification(phoneNumber, skipPermissionCheck, "+91");
        }
    }

    public void ResendCode() {
        startTimer();
      //  mVerification.resend("voice");
    }

    void hideProgressBarAndShowMessage(int message) {
        hideProgressBar();
        Button messageText = (Button) findViewById(R.id.btn_log_submit);
        messageText.setText(message);
    }
    void hideProgressBarAndShowMessage1(String message) {
        hideProgressBar();
        Button messageText = (Button) findViewById(R.id.btn_log_submit);
        messageText.setText(message);
    }
    void hideProgressBar() {
        ProgressBar progressBar = (ProgressBar) findViewById(R.id.progressIndicator);
        progressBar.setVisibility(View.INVISIBLE);
    }
   void showProgress() {
        ProgressBar progressBar = (ProgressBar) findViewById(R.id.progressIndicator);
        progressBar.setVisibility(View.VISIBLE);
    }

//    @Override
//    public void onInitiated(String response) {
//        Log.d(TAG, "Initialized!" + response.toString());
//    }
//    @Override
//    public void onInitiationFailed(Exception exception) {
//        Log.e(TAG, "Verification initialization failed: " + exception.getMessage());
//        hideProgressBarAndShowMessage(R.string.failed);
//    }
//    @Override
//    public void onVerified(String response) {
////        enableInputField(false);
//        Log.d(TAG, "Verified!\n" + response);
//        Log.d(TAG, "Verified!\n" + new SharedPrefClass(OtpVerificationActivity.this).getOTP());
//
//        String[] separated = new SharedPrefClass(OtpVerificationActivity.this).getOTP().split("\\.");
//        otp1.setText(separated[0]);
//        hideKeypad();
//    }

//    @Override
//    public void onVerificationFailed(Exception exception) {
//        Log.e(TAG, "Verification failed: " + exception.getMessage());
//        hideKeypad();
////      enableInputField(true);
//        hideProgressBar();
//    }
    private void startTimer() {
        resend_timer.setClickable(false);
        resend_timer.setTextColor(ContextCompat.getColor(OtpVerificationActivity.this, R.color.dark_gray));
        new CountDownTimer(30000, 1000) {
            int secondsLeft = 0;

            public void onTick(long ms) {
                if (Math.round((float) ms / 1000.0f) != secondsLeft) {
                    secondsLeft = Math.round((float) ms / 1000.0f);
                    resend_timer.setText("Read OTP ( " + secondsLeft + " )");
                    resend_timer.setTextColor(ContextCompat.getColor(OtpVerificationActivity.this, R.color.dark_gray));

                }
            }

            public void onFinish() {
                resend_timer.setClickable(true);
                resend_timer.setText("Resend OTP");
                resend_timer.setTextColor(ContextCompat.getColor(OtpVerificationActivity.this, R.color.dark_gray));
            }
        }.start();
    }

    private void hideKeypad() {
        View view = getCurrentFocus();
        if (view != null) {
            InputMethodManager imm = (InputMethodManager) getSystemService(Context.INPUT_METHOD_SERVICE);
            imm.hideSoftInputFromWindow(view.getWindowToken(), 0);
        }

    }

    public void OtpVerification() {
        final ProgressDialog progressdialog = new ProgressDialog(OtpVerificationActivity.this);
        progressdialog.setMessage("Please Wait....");
        progressdialog.show();

        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
        String  token = pref.getString("regId", null);
        apiInterface = ApiClient.getClient().create(ApiInterface.class);
        Log.v("key","phoneNumber"+phoneNumber);
        apiInterface.getLoginResponse(phoneNumber,token).enqueue(new Callback<LoginModel>() {
            @Override
            public void onResponse(Call<LoginModel> call, Response<LoginModel> response) {
                LoginModel loginModel=response.body();
                int status=loginModel.getRecord().get(0).getStatus();
                if(status==1) {
                    List<Record> recordsList = response.body().getRecord();
                    String key = recordsList.get(0).getKey();
                    Log.v("key","key"+key);
                    String Name = recordsList.get(0).getName();
                    AppController.getInstance().isKeyBoolean("userlogin", true);
                    new SharedPrefClass(OtpVerificationActivity.this).createLoginSession(key);
                    new SharedPrefClass(OtpVerificationActivity.this).setUserName(Name);
                    Navigatior.getClassInstance().navigateToActivity(OtpVerificationActivity.this, HomeActivity.class);
                    // Toast.makeText(LoginActivity.this, "Successful", Toast.LENGTH_LONG).show();
                }
                if (status==0){
                    Toast.makeText(OtpVerificationActivity.this, "Please Enter Registered Mobile Number", Toast.LENGTH_LONG).show();
                }

            }

            @Override
            public void onFailure(Call<LoginModel> call, Throwable t) {
                //     new DialogClass(LoginActivity.this).stopProgress();
                Toast.makeText(OtpVerificationActivity.this, "Opps!!! Server Connection Error", Toast.LENGTH_LONG).show();

            }
        });
    }









}
