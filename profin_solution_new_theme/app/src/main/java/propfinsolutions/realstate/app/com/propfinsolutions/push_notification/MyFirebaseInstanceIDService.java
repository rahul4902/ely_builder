package propfinsolutions.realstate.app.com.propfinsolutions.push_notification;

import android.content.Intent;
import android.content.SharedPreferences;
import android.util.Log;

import androidx.annotation.NonNull;
import androidx.localbroadcastmanager.content.LocalBroadcastManager;

import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;

public class MyFirebaseInstanceIDService extends FirebaseMessagingService {

    private static final String TAG = MyFirebaseInstanceIDService.class.getSimpleName();

    @Override
    public void onNewToken(@NonNull String token) {
        super.onNewToken(token);

        // Log the new token
        Log.d(TAG, "New Token: " + token);

        // Send the token to your server or perform other logic
        sendRegistrationToServer(token);

        // Store the token in shared preferences
//        storeRegIdInPref(token);
//
//        // Notify UI that registration has completed
//        Intent registrationComplete = new Intent(Config.REGISTRATION_COMPLETE);
//        registrationComplete.putExtra("token", token);
//        LocalBroadcastManager.getInstance(this).sendBroadcast(registrationComplete);

    }

    @Override
    public void onMessageReceived(@NonNull RemoteMessage remoteMessage) {
        super.onMessageReceived(remoteMessage);

        // Handle incoming messages here if needed
        Log.d(TAG, "Message received from: " + remoteMessage.getFrom());
        // You can access the message data and notification payload
    }

    private void sendRegistrationToServer(final String token) {
        // Send the token to your server or perform other logic
        Log.e(TAG, "sendRegistrationToServer: " + token);
    }

    private void storeRegIdInPref(String token) {
        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
        SharedPreferences.Editor editor = pref.edit();
        editor.putString("regId", token);
        editor.commit();
    }
}


//package propfinsolutions.realstate.app.com.propfinsolutions.push_notification;
//
//import android.content.SharedPreferences;
//import android.util.Log;
//
//import com.google.firebase.iid.FirebaseInstanceId;
//import com.google.firebase.iid.FirebaseInstanceIdService;
//
//
//public class MyFirebaseInstanceIDService extends FirebaseInstanceIdService {
//
//    @Override
//    public void onTokenRefresh() {
//        String tkn = FirebaseInstanceId.getInstance().getToken();
//        Log.d("Not","Token ["+tkn+"]");
//        sendRegistrationToServer(tkn);
//        storeRegIdInPref(tkn);
//   }
//       private static final String TAG = MyFirebaseInstanceIDService.class.getSimpleName();
//
////    @Override
////    public void onTokenRefresh() {
////        super.onTokenRefresh();
////        String refreshedToken = FirebaseInstanceId.getInstance().getToken();
////
////        // Saving reg id to shared preferences
////        storeRegIdInPref(refreshedToken);
////
////        // sending reg id to your server
////        sendRegistrationToServer(refreshedToken);
////
////        // Notify UI that registration has completed, so the progress indicator can be hidden.
////        Intent registrationComplete = new Intent(Config.REGISTRATION_COMPLETE);
////        registrationComplete.putExtra("token", refreshedToken);
////        LocalBroadcastManager.getInstance(this).sendBroadcast(registrationComplete);
////    }
//
//    private void sendRegistrationToServer(final String token) {
//        // sending gcm token to server
//        Log.e(TAG, "sendRegistrationToServer: " + token);
//    }
//
//    private void storeRegIdInPref(String token) {
//        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
//        SharedPreferences.Editor editor = pref.edit();
//        editor.putString("regId", token);
//        editor.commit();
//    }
//}