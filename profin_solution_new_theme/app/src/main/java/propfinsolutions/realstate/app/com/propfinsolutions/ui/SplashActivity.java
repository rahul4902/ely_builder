package propfinsolutions.realstate.app.com.propfinsolutions.ui;

import android.Manifest;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.os.Build;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.WindowManager;
import android.webkit.WebView;
import android.widget.ImageView;

import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.localbroadcastmanager.content.LocalBroadcastManager;

import com.google.firebase.messaging.FirebaseMessaging;

import java.util.ArrayList;
import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.Config;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.AppController;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;

public class SplashActivity extends AppCompatActivity {

    @BindView(R.id.logo)
    ImageView logoIV;

    @BindView(R.id.idSpProgress)
    WebView mWebView;

    private static final String TAG = "SplashActivity";
    private static final int PERMISSION_REQUEST_CODE = 123;
    private final int SPLASH_TIME = 3000;

    @Override
    protected void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        getSupportActionBar().hide();
        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN); // enable full screen
        setContentView(R.layout.activity_splash);
        ButterKnife.bind(this);

        logoIV.setImageResource(R.drawable.logo);

        final MyJavaScriptInterface myJavaScriptInterface = new MyJavaScriptInterface(this);
        mWebView.addJavascriptInterface(myJavaScriptInterface, "AndroidFunction");
        mWebView.getSettings().setJavaScriptEnabled(true);
        mWebView.loadUrl("file:///android_asset/progress_horizontal/index.html");

        // Run navigation in parallel with permission check
        navigateAfterDelay();

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
            checkNotificationPermission();
        } else {
            fetchFCMToken();
            checkAndRequestPermissions();
        }
    }

    // Ensure splash screen navigates after SPLASH_TIME delay
    private void navigateAfterDelay() {
        new Handler().postDelayed(() -> {
            if (AppController.getInstance().getKeyBoolean("userlogin")) {
                Navigatior.getClassInstance().navigateToActivity(SplashActivity.this, HomeActivity.class);
            } else {
                Navigatior.getClassInstance().navigateToActivity(SplashActivity.this, LoginActivity.class);
            }
            finish();
        }, SPLASH_TIME);
    }

    private void fetchFCMToken() {
        FirebaseMessaging.getInstance().getToken()
                .addOnCompleteListener(task -> {
                    if (!task.isSuccessful()) {
                        Log.w(TAG, "Fetching FCM registration token failed", task.getException());
                        return;
                    }
                    // Get new FCM registration token
                    String token = task.getResult();
                    Log.d(TAG, "FCM Token: " + token);
                    storeRegIdInPref(token);
                    sendRegistrationToServer(token);

                    // Notify UI that registration has completed
                    Intent registrationComplete = new Intent(Config.REGISTRATION_COMPLETE);
                    registrationComplete.putExtra("token", token);
                    LocalBroadcastManager.getInstance(this).sendBroadcast(registrationComplete);
                });
    }

    private void storeRegIdInPref(String token) {
        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
        SharedPreferences.Editor editor = pref.edit();
        editor.putString("regId", token);
        editor.apply();
        editor.commit();
    }

    private void sendRegistrationToServer(final String token) {
        Log.e(TAG, "sendRegistrationToServer: " + token);
        // Send token to your server here
    }

    private void checkNotificationPermission() {
        if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS)
                == PackageManager.PERMISSION_GRANTED) {
            fetchFCMToken();
            checkAndRequestPermissions();
        } else {
            ActivityCompat.requestPermissions(this,
                    new String[]{Manifest.permission.POST_NOTIFICATIONS}, PERMISSION_REQUEST_CODE);
        }
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        if (requestCode == PERMISSION_REQUEST_CODE) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                fetchFCMToken();
            } else {
                Log.w(TAG, "Notification permission denied.");
            }
            checkAndRequestPermissions();
        }
    }

    private boolean checkAndRequestPermissions() {
        int permissionSendMessage = ContextCompat.checkSelfPermission(this, Manifest.permission.SEND_SMS);
        int receiveSMS = ContextCompat.checkSelfPermission(this, Manifest.permission.RECEIVE_SMS);
        int readSMS = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_SMS);
        int cameraPermission = ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA);
        int readPermission = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE);
        int writePermission = ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE);
        int location = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION);
        int ACCESS_FINE_LOCATION = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION);

        List<String> listPermissionsNeeded = new ArrayList<>();
        if (permissionSendMessage != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.SEND_SMS);
        if (receiveSMS != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.RECEIVE_SMS);
        if (readSMS != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.READ_SMS);
        if (cameraPermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.CAMERA);
        if (location != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.ACCESS_COARSE_LOCATION);
        if (ACCESS_FINE_LOCATION != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.ACCESS_FINE_LOCATION);
        if (readPermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.READ_EXTERNAL_STORAGE);
        if (writePermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.WRITE_EXTERNAL_STORAGE);

        if (!listPermissionsNeeded.isEmpty()) {
            ActivityCompat.requestPermissions(this, listPermissionsNeeded.toArray(new String[0]), 1);
            return true;
        }

        return true; // All permissions granted
    }

    public class MyJavaScriptInterface {
        Context mContext;

        MyJavaScriptInterface(Context c) {
            mContext = c;
        }
    }
}


//package propfinsolutions.realstate.app.com.propfinsolutions.ui;
//
//
//import android.Manifest;
//import android.content.Context;
//import android.content.SharedPreferences;
//import android.content.pm.PackageManager;
//import android.os.Build;
//import android.os.Bundle;
//import android.os.Handler;
//import android.util.Log;
//import android.view.WindowManager;
//import android.webkit.WebView;
//import android.widget.ImageView;
//
//import androidx.annotation.NonNull;
//import androidx.annotation.Nullable;
//import androidx.appcompat.app.AppCompatActivity;
//import androidx.core.app.ActivityCompat;
//import androidx.core.content.ContextCompat;
//
//import com.google.firebase.messaging.FirebaseMessaging;
//
//import java.util.ArrayList;
//import java.util.HashMap;
//import java.util.List;
//import java.util.Map;
//
//import butterknife.BindView;
//import butterknife.ButterKnife;
//import propfinsolutions.realstate.app.com.propfinsolutions.R;
//import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.Config;
//import propfinsolutions.realstate.app.com.propfinsolutions.utils.AppController;
//import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
//import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
//
///**
// * Created by Divakar on 6/19/2017.
// */
//
//public class SplashActivity extends AppCompatActivity {
//
//    @BindView(R.id.logo)
//    ImageView logoIV;
//
//    @BindView(R.id.idSpProgress)
//    WebView mWebView;
//    private static final String TAG = "SplashActivity";
//    private static final int PERMISSION_REQUEST_CODE = 123;
//    Context context;
//    @Override
//    protected void onCreate(@Nullable Bundle savedInstanceState) {
//        super.onCreate(savedInstanceState);
//        getSupportActionBar().hide();
//        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
//                WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen
//        setContentView(R.layout.activity_splash);
//        ButterKnife.bind(this);
//
//        logoIV.setImageResource(R.drawable.logo);
//
//        final MyJavaScriptInterface myJavaScriptInterface
//                = new MyJavaScriptInterface(this);
//        mWebView.addJavascriptInterface(myJavaScriptInterface, "AndroidFunction");
//
//        mWebView.getSettings().setJavaScriptEnabled(true);
//        mWebView.loadUrl("file:///android_asset/progress_horizontal/index.html");
//
//        //checkAndRequestPermissions();
//        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.TIRAMISU) {
//            checkNotificationPermission();
//        } else {
//            fetchFCMToken();
//            checkAndRequestPermissions();
//        }
//    }
//
//    private void fetchFCMToken() {
//        FirebaseMessaging.getInstance().getToken()
//                .addOnCompleteListener(task -> {
//                    if (!task.isSuccessful()) {
//                        Log.w(TAG, "Fetching FCM registration token failed", task.getException());
//                        return;
//                    }
//
//                    // Get new FCM registration token
//                    String token = task.getResult();
//                    Log.d(TAG, "FCM Token: " + token);
//                    storeRegIdInPref(token);
//                    sendRegistrationToServer(token);
//                });
//    }
//
//    private void storeRegIdInPref(String token) {
//        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
//        SharedPreferences.Editor editor = pref.edit();
//        editor.putString("regId", token);
//        editor.apply();
//        editor.commit();
//    }
//
//    private void sendRegistrationToServer(final String token) {
//        Log.e(TAG, "sendRegistrationToServer: " + token);
//        // Send token to your server here
//    }
//    private void checkNotificationPermission() {
//        if (ContextCompat.checkSelfPermission(this, Manifest.permission.POST_NOTIFICATIONS)
//                == PackageManager.PERMISSION_GRANTED) {
//            // Permission granted
//            fetchFCMToken();
//            checkAndRequestPermissions();
//        } else {
//            // Request notification permission
//            ActivityCompat.requestPermissions(this, new String[]{Manifest.permission.POST_NOTIFICATIONS}, PERMISSION_REQUEST_CODE);
//        }
//    }
//
//    @Override
//    protected void onStart() {
//        super.onStart();
//        // LOGOUT USER
//    //    new SharedPrefClass(SplashActivity.this).logoutUser();
//    }
//    public class MyJavaScriptInterface {
//        Context mContext;
//        MyJavaScriptInterface(Context c) {
//            mContext = c;
//        }
//    }
//// Timer using Handler
//    private final int SPLASH_TIME = 3000;
//    // Handling splash timer.
//    private void navigate() {
//        new Handler().postDelayed(
//                new Runnable() {
//                    @Override
//                    public void run() {
//
//                        Log.v("datat","date"+new SharedPrefClass(SplashActivity.this).getUserName());
//                        if(AppController.getInstance().getKeyBoolean("userlogin")) {
//                        Navigatior.getClassInstance().navigateToActivity(SplashActivity.this, HomeActivity.class);}
//                        else {
//                        Navigatior.getClassInstance().navigateToActivity(SplashActivity.this, LoginActivity.class);
//                        }
//                    }
//                }, SPLASH_TIME);
//    }
//
//
////    private boolean checkAndRequestPermissions() {
////        int permissionSendMessage = ContextCompat.checkSelfPermission(this,
////                Manifest.permission.SEND_SMS);
////        int receiveSMS = ContextCompat.checkSelfPermission(this,
////                Manifest.permission.RECEIVE_SMS);
////        int readSMS = ContextCompat.checkSelfPermission(this,
////                Manifest.permission.READ_SMS);
////
////        int cameraPermission = ContextCompat.checkSelfPermission(this, android.Manifest.permission.CAMERA);
////        int readPermission = ContextCompat.checkSelfPermission(this, android.Manifest.permission.READ_EXTERNAL_STORAGE);
////        int writePermission = ContextCompat.checkSelfPermission(this, android.Manifest.permission.WRITE_EXTERNAL_STORAGE);
////        int location = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION);
////        int ACCESS_FINE_LOCATION = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION);
////        //  int recordpermission = ContextCompat.checkSelfPermission(this, Manifest.permission.RECORD_AUDIO);
////
////        int medeiapermission = ContextCompat.checkSelfPermission(this, Manifest.permission.MEDIA_CONTENT_CONTROL);
////
////
////        final List<String> listPermissionsNeeded = new ArrayList<>();
////
////        if (permissionSendMessage != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.SEND_SMS);
////        }
////        if (receiveSMS != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.RECEIVE_MMS);
////        }
////        if (readSMS != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.READ_SMS);
////        }
////
////
////        if (cameraPermission != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.CAMERA);
////        }
////
////        if (location != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.ACCESS_COARSE_LOCATION);
////        }
////        if (ACCESS_FINE_LOCATION != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.ACCESS_FINE_LOCATION);
////        }
////
////        if (readPermission != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.READ_EXTERNAL_STORAGE);
////        }
////
////        if (writePermission != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.WRITE_EXTERNAL_STORAGE);
////        }
////
//////        if (recordpermission != PackageManager.PERMISSION_GRANTED) {
//////            listPermissionsNeeded.add(Manifest.permission.RECORD_AUDIO);
//////        }
////
////
////
////        if (medeiapermission != PackageManager.PERMISSION_GRANTED) {
////            listPermissionsNeeded.add(Manifest.permission.MEDIA_CONTENT_CONTROL);
////        }
////
////        if (!listPermissionsNeeded.isEmpty()) {
////            ActivityCompat.requestPermissions(this,
////                    listPermissionsNeeded.toArray(new String[listPermissionsNeeded.size()]),
////                    1);
////            return true;
////        }
////        return true;
////    }
//
//
////    @Override
////    public void onRequestPermissionsResult(int requestCode,
////                                           String permissions[], int[] grantResults) {
////        switch (requestCode) {
////            case 1: {
////                Map<String, Integer> perms = new HashMap<>();
////                // Initial
////                perms.put(Manifest.permission.SEND_SMS, PackageManager.PERMISSION_GRANTED);
////                perms.put(Manifest.permission.RECEIVE_SMS, PackageManager.PERMISSION_GRANTED);
////                perms.put(Manifest.permission.READ_SMS, PackageManager.PERMISSION_GRANTED);
////                perms.put(Manifest.permission.CAMERA, PackageManager.PERMISSION_GRANTED);
////                perms.put(Manifest.permission.READ_EXTERNAL_STORAGE, PackageManager.PERMISSION_GRANTED);
////                perms.put(Manifest.permission.WRITE_EXTERNAL_STORAGE, PackageManager.PERMISSION_GRANTED);
////                // Fill with results
////                for (int i = 0; i < permissions.length; i++)
////                    perms.put(permissions[i], grantResults[i]);
////                // Check for ACCESS_FINE_LOCATION
////                if (perms.get(Manifest.permission.SEND_SMS) == PackageManager.PERMISSION_GRANTED
////                        && perms.get(Manifest.permission.RECEIVE_SMS) == PackageManager.PERMISSION_GRANTED
////                        && perms.get(Manifest.permission.READ_SMS) == PackageManager.PERMISSION_GRANTED && perms.get(Manifest.permission.CAMERA) == PackageManager.PERMISSION_GRANTED && perms.get(Manifest.permission.READ_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED && perms.get(Manifest.permission.WRITE_EXTERNAL_STORAGE) == PackageManager.PERMISSION_GRANTED) {
////                    // All Permissions Granted
////
////                    navigate();
////
////                } else {
////
////                }
////            }
////            break;
////            default:
////                super.onRequestPermissionsResult(requestCode, permissions, grantResults);
////        }
////    }
//
//    private boolean checkAndRequestPermissions() {
//        int permissionSendMessage = ContextCompat.checkSelfPermission(this, Manifest.permission.SEND_SMS);
//        int receiveSMS = ContextCompat.checkSelfPermission(this, Manifest.permission.RECEIVE_SMS);
//        int readSMS = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_SMS);
//        int cameraPermission = ContextCompat.checkSelfPermission(this, Manifest.permission.CAMERA);
//        int readPermission = ContextCompat.checkSelfPermission(this, Manifest.permission.READ_EXTERNAL_STORAGE);
//        int writePermission = ContextCompat.checkSelfPermission(this, Manifest.permission.WRITE_EXTERNAL_STORAGE);
//        int location = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION);
//        int ACCESS_FINE_LOCATION = ContextCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION);
//        int mediaPermission = ContextCompat.checkSelfPermission(this, Manifest.permission.MEDIA_CONTENT_CONTROL);
//
//        List<String> listPermissionsNeeded = new ArrayList<>();
//        if (permissionSendMessage != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.SEND_SMS);
//        if (receiveSMS != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.RECEIVE_SMS);
//        if (readSMS != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.READ_SMS);
//        if (cameraPermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.CAMERA);
//        if (location != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.ACCESS_COARSE_LOCATION);
//        if (ACCESS_FINE_LOCATION != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.ACCESS_FINE_LOCATION);
//        if (readPermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.READ_EXTERNAL_STORAGE);
//        if (writePermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.WRITE_EXTERNAL_STORAGE);
//        if (mediaPermission != PackageManager.PERMISSION_GRANTED) listPermissionsNeeded.add(Manifest.permission.MEDIA_CONTENT_CONTROL);
//
//        if (!listPermissionsNeeded.isEmpty()) {
//            ActivityCompat.requestPermissions(this, listPermissionsNeeded.toArray(new String[0]), 1);
//            return true;
//        }
//
//        navigate(); // Permissions granted, proceed with navigation
//        return true;
//    }
//
//    @Override
//    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
//        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
//        if (requestCode == PERMISSION_REQUEST_CODE) {
//            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
//                // Permission granted, fetch FCM token
//                fetchFCMToken();
//            } else {
//                Log.w(TAG, "Notification permission denied.");
//            }
//            checkAndRequestPermissions();
//        } else {
//            super.onRequestPermissionsResult(requestCode, permissions, grantResults);
//        }
//    }
//
//}
