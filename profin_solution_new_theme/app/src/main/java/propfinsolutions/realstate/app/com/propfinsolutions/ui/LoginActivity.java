package propfinsolutions.realstate.app.com.propfinsolutions.ui;

import android.Manifest;
import android.app.ProgressDialog;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.graphics.Color;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.WindowManager;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ProgressBar;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;

//import com.msg91.sendotpandroid.library.PhoneNumberFormattingTextWatcher;
//import com.msg91.sendotpandroid.library.PhoneNumberUtils;
//import com.msg91.sendotpandroid.library.internal.Iso2Phone;

import com.google.firebase.FirebaseException;
import com.google.firebase.auth.PhoneAuthCredential;
import com.google.firebase.auth.PhoneAuthProvider;

import java.util.List;
import java.util.Locale;
import java.util.Objects;
import java.util.concurrent.TimeUnit;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.LoginModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.Record;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.Config;
import propfinsolutions.realstate.app.com.propfinsolutions.spiner.CountrySpinner;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LoginActivity extends AppCompatActivity {
    private static final int MY_PERMISSIONS_REQUEST_LOCATION = 100;
    @BindView(R.id.etLoginMobile)
    EditText etLoginMobile;
    ApiInterface apiInterface;
    String token;
    public static final String INTENT_PHONENUMBER = "phonenumber";
    public static final String SEND_OTP_CODE = "send_otp_code";
    public static final String INTENT_COUNTRY_CODE = "code";

    public  static final String ActivityId="106";


    private Button mSmsButton;
    private String mCountryIso;
    private TextWatcher mNumberTextWatcher;
    ProgressDialog mProgressDialog;
    private static final int PERMISSION_REQUEST_ID = 100;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getSupportActionBar().hide();
        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen
         setContentView(R.layout.activity_login);

        initiateVerification(true);
        checkLocationPermission();

        /*
//        mCountryIso = PhoneNumberUtils.getDefaultCountryIso(this);
        final String defaultCountryName = new Locale("", mCountryIso).getDisplayName();
        final CountrySpinner spinner = (CountrySpinner) findViewById(R.id.spinner);
        spinner.init(defaultCountryName);

        spinner.addCountryIsoSelectedListener(new CountrySpinner.CountryIsoSelectedListener() {
            @Override
            public void onCountryIsoSelected(String selectedIso) {
                if (selectedIso != null) {
                    mCountryIso = selectedIso;
                //    resetNumberTextWatcher(mCountryIso);

                    // force update:
                    //mNumberTextWatcher.afterTextChanged();
                }
            }
        });

         */

        ButterKnife.bind(LoginActivity.this);

        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
        token = pref.getString("regId", null);

        apiInterface = ApiClient.getClient().create(ApiInterface.class);


    }

    public boolean checkLocationPermission() {
        if (ContextCompat.checkSelfPermission(this,
                Manifest.permission.ACCESS_FINE_LOCATION)
                != PackageManager.PERMISSION_GRANTED) {
            // Should we show an explanation?
            if (ActivityCompat.shouldShowRequestPermissionRationale(this,
                    Manifest.permission.ACCESS_FINE_LOCATION)) {


                Log.v("Dat3","Data1");
            } else {
                Log.v("Data4","Data1");
                // No explanation needed, we can request the permission.
                ActivityCompat.requestPermissions(this,
                        new String[]{Manifest.permission.ACCESS_FINE_LOCATION},
                        MY_PERMISSIONS_REQUEST_LOCATION);
            }
            return false;
        } else {
            return true;
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, String permissions[], int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        switch (requestCode) {
            case MY_PERMISSIONS_REQUEST_LOCATION: {
                // If request is cancelled, the result arrays are empty.
                if (grantResults.length > 0
                        && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                    Log.v("Data2", "Data1");
                    // permission was granted, yay! Do the
                    // location-related task you need to do.
                    if (ContextCompat.checkSelfPermission(this,
                            Manifest.permission.ACCESS_FINE_LOCATION)
                            == PackageManager.PERMISSION_GRANTED) {
                    }

                } else {

                    Log.v("Data", "Data");
                    // permission denied, boo! Disable the
                    // functionality that depends on this permission.

                }
                return;
            }
        }
    }

//    private void resetNumberTextWatcher(String countryIso) {
//        if (mNumberTextWatcher != null) {
//            etLoginMobile.removeTextChangedListener(mNumberTextWatcher);
//        }
////        mNumberTextWatcher = new PhoneNumberFormattingTextWatcher(countryIso) {
//            @Override
//            public void onTextChanged(CharSequence s, int start, int before, int count) {
//                super.onTextChanged(s, start, before, count);
//            }
//
//            @Override
//            public void beforeTextChanged(CharSequence s, int start, int count, int after) {
//                super.beforeTextChanged(s, start, count, after);
//            }
//
//            @Override
//            public synchronized void afterTextChanged(Editable s) {
//                super.afterTextChanged(s);
//                if (isPossiblePhoneNumber()) {
//                    setButtonsEnabled(true);
//                    etLoginMobile.setTextColor(Color.WHITE);
//                } else {
//                    setButtonsEnabled(false);
//                    etLoginMobile.setTextColor(Color.WHITE);
//                }
//            }
//        };

//        etLoginMobile.addTextChangedListener(mNumberTextWatcher);
  //  }

    private void setButtonsEnabled(boolean enabled) {
        mSmsButton.setEnabled(enabled);
    }

//    private boolean isPossiblePhoneNumber() {
//        return PhoneNumberUtils.isPossibleNumber(etLoginMobile.getText().toString(), mCountryIso);
//    }

    void initiateVerification(boolean skipPermissionCheck) {
        createVerification(skipPermissionCheck);
    }

    void createVerification(boolean skipPermissionCheck) {
        if (!skipPermissionCheck && ContextCompat.checkSelfPermission(this, Manifest.permission.READ_SMS) ==
                PackageManager.PERMISSION_DENIED) {
            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.READ_SMS}, 0);

        } else {

        }
    }
    private String getE164Number() {
        return etLoginMobile.getText().toString().replaceAll("\\D", "").trim();
        // return PhoneNumberUtils.formatNumberToE164(mPhoneNumber.getText().toString(), mCountryIso);
    }

    void progressShow(){
        mProgressDialog = new ProgressDialog(this);
        mProgressDialog.setIndeterminate(true);
        mProgressDialog.setMessage("Loading...");
        mProgressDialog.setTitle(R.string.app_name);
        mProgressDialog.show();
    }

    @OnClick(R.id.btnLoginSubmit)

    public void submit(){
        String mobile = "";
        if(etLoginMobile.length()>0 && etLoginMobile.length() == 10)  {
//            OtpVerification();
            progressShow();
            mobile = etLoginMobile.getText().toString();
            Log.d("login credential", "login number :" + mobile+ " fcm_token : "+token);
            apiInterface.getLoginResponse(mobile,token).enqueue(new Callback<LoginModel>() {
            @Override
            public void onResponse(Call<LoginModel> call, Response<LoginModel> response) {
                    LoginModel loginModel=response.body();
                    int status=loginModel.getRecord().get(0).getStatus();
/*
                    Log.v("MESDFSF","mmdsfs"+response);
                     Log.v("status","status"+status);*/
                    if(status==1) {
                        mProgressDialog.dismiss();

                        //open verification class
                        openActivity(getE164Number());

                        List<Record> recordsList = response.body().getRecord();
                        String key = recordsList.get(0).getKey();
                        Log.v("key","key"+key);
                    }
                    if (status==0){
                        mProgressDialog.dismiss();

                        Toast.makeText(LoginActivity.this, "Please Enter Registered Mobile Number", Toast.LENGTH_LONG).show();
                    }

            }

            @Override
            public void onFailure(Call<LoginModel> call, Throwable t) {
                mProgressDialog.dismiss();
                Toast.makeText(LoginActivity.this, "Opps!!! Server Connection Error", Toast.LENGTH_LONG).show();

            }
        });
        }
        else {etLoginMobile.setError("Enter Mobile No.");
      }
   }


    private void openActivity(String phoneNumber) {
        PhoneAuthProvider.getInstance().verifyPhoneNumber(
                "+91" + phoneNumber.toString(), 60,
                TimeUnit.SECONDS,
                LoginActivity.this, new PhoneAuthProvider.OnVerificationStateChangedCallbacks() {
                    @Override
                    public void onVerificationCompleted(@NonNull PhoneAuthCredential phoneAuthCredential) {
                        
                    }
                    @Override
                    public void onVerificationFailed(@NonNull FirebaseException e) {
                        Log.d("firebase auth error : ", Objects.requireNonNull(e.getMessage()));
                        Toast.makeText(LoginActivity.this, e.getMessage(), Toast.LENGTH_SHORT).show();
                    }

                    @Override
                    public void onCodeSent(@NonNull String sendOtpCode, @NonNull PhoneAuthProvider.ForceResendingToken forceResendingToken) {
                        super.onCodeSent(sendOtpCode, forceResendingToken);
                        Intent verification = new Intent(LoginActivity.this, OtpVerificationActivity.class);
                        verification.putExtra(INTENT_PHONENUMBER, phoneNumber);
                        verification.putExtra(SEND_OTP_CODE, sendOtpCode);
                        startActivity(verification);
                    }
                }
        );

    }

    public void OtpVerification() {
        final ProgressDialog progressdialog = new ProgressDialog(LoginActivity.this);
        progressdialog.setMessage("Please Wait....");
        progressdialog.show();

        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
        String token = pref.getString("regId", null);
        apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getLoginResponse("9953985609", token).enqueue(new Callback<LoginModel>() {
            @Override
            public void onResponse(Call<LoginModel> call, Response<LoginModel> response) {
                LoginModel loginModel = response.body();
                int status = loginModel.getRecord().get(0).getStatus();
                if (status == 1) {
                    List<Record> recordsList = response.body().getRecord();
                    String key = recordsList.get(0).getKey();
                    Log.v("key", "key" + key);
                    String Name = recordsList.get(0).getName();
                    new SharedPrefClass(LoginActivity.this).createLoginSession(key);
                    new SharedPrefClass(LoginActivity.this).setUserName(Name);
                    Navigatior.getClassInstance().navigateToActivity(LoginActivity.this, HomeActivity.class);
                    // Toast.makeText(LoginActivity.this, "Successful", Toast.LENGTH_LONG).show();
                }
                if (status == 0) {
                    Toast.makeText(LoginActivity.this, "Please Enter Registered Mobile Number", Toast.LENGTH_LONG).show();
                }

            }

            @Override
            public void onFailure(Call<LoginModel> call, Throwable t) {
                Log.v("key", "key" + t.getMessage());
                //     new DialogClass(LoginActivity.this).stopProgress();
                Toast.makeText(LoginActivity.this, "Opps!!! Server Connection Error", Toast.LENGTH_LONG).show();

            }
        });
    }
}
