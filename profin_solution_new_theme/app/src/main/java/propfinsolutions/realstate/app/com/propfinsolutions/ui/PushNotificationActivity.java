package propfinsolutions.realstate.app.com.propfinsolutions.ui;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.text.TextUtils;
import android.util.Log;
import android.view.WindowManager;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;
import androidx.localbroadcastmanager.content.LocalBroadcastManager;

import com.google.firebase.messaging.FirebaseMessaging;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.Config;
import propfinsolutions.realstate.app.com.propfinsolutions.push_notification.NotificationUtils;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.NotificationClassHelper;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;


public class PushNotificationActivity extends AppCompatActivity {
        private static final String TAG = PushNotificationActivity.class.getSimpleName();
        private BroadcastReceiver mRegistrationBroadcastReceiver;
        private TextView txtMessage;
        private EditText txtRegId;
        private TextView userName;
        @Override
        protected void onCreate(Bundle savedInstanceState) {
            super.onCreate(savedInstanceState);

            getSupportActionBar().hide();
            this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                    WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen
            setContentView(R.layout.activity_push_notification);

            SharedPreferences prefs = this.getSharedPreferences("leadid", Context.MODE_PRIVATE);

            int lead_id = prefs.getInt("idlead", 0);

            Navigatior.getClassInstance().navigateToActivityWithDataNotAnim(PushNotificationActivity.this, ViewDetailActivityOne.class, null, lead_id);

            userName=(TextView)findViewById(R.id.userName);
            txtRegId = (EditText) findViewById(R.id.txt_reg_id);
            txtMessage = (TextView) findViewById(R.id.txt_push_message);
            String UserName = new SharedPrefClass(getApplicationContext()).getUserName().toString();
            userName.setText(UserName);

            mRegistrationBroadcastReceiver = new BroadcastReceiver() {
                @Override
                public void onReceive(Context context, Intent intent) {

                    // checking for type intent filter
                    if (intent.getAction().equals(Config.REGISTRATION_COMPLETE)) {
                        // gcm successfully registered
                        // now subscribe to `global` topic to receive app wide notifications
                        FirebaseMessaging.getInstance().subscribeToTopic(Config.TOPIC_GLOBAL);

                        displayFirebaseRegId();

                    } else if (intent.getAction().equals(Config.PUSH_NOTIFICATION)) {
                        // new push notification is received

                        String title = "PROPLIN SOLUTIONS";
                        String message = intent.getStringExtra("message");

                        Toast.makeText(getApplicationContext(), "Push notification: " + message, Toast.LENGTH_LONG).show();

                        new NotificationClassHelper(PushNotificationActivity.this).showNitification(title, message);

                        txtMessage.setText(title+", \n"+message);
                    }
                }
            };

            displayFirebaseRegId();
        }

        // Fetches reg id from shared preferences
        // and displays on the screen
    private void displayFirebaseRegId() {
        SharedPreferences pref = getApplicationContext().getSharedPreferences(Config.SHARED_PREF, 0);
        String regId = pref.getString("regId", null);

        Log.e(TAG, "Firebase reg id: " + regId);

        if (!TextUtils.isEmpty(regId)){
            txtRegId.setText("Firebase Reg Id: " + regId);
            System.out.println(regId);
        // FILE START
        }
        else
        { txtRegId.setText("Firebase Reg Id is not received yet!");}
    }

    @Override
    protected void onResume() {
        super.onResume();

        // register GCM registration complete receiver
        LocalBroadcastManager.getInstance(this).registerReceiver(mRegistrationBroadcastReceiver,
                new IntentFilter(Config.REGISTRATION_COMPLETE));

        // register new push message receiver
        // by doing this, the activity will be notified each time a new message arrives
        LocalBroadcastManager.getInstance(this).registerReceiver(mRegistrationBroadcastReceiver,
                new IntentFilter(Config.PUSH_NOTIFICATION));

        // clear the notification area when the app is opened
        NotificationUtils.clearNotifications(getApplicationContext());
    }

    @Override
    protected void onPause() {
        LocalBroadcastManager.getInstance(this).unregisterReceiver(mRegistrationBroadcastReceiver);
        super.onPause();
    }
}