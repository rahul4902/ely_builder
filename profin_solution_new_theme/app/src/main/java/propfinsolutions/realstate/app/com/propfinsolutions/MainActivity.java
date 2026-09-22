package propfinsolutions.realstate.app.com.propfinsolutions;

import androidx.appcompat.app.AppCompatActivity;
import android.os.Bundle;
import android.util.Log;

import com.google.firebase.FirebaseApp;
import com.google.firebase.messaging.FirebaseMessaging;

public class MainActivity extends AppCompatActivity {
    private String TAG = MainActivity.class.getSimpleName();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

//        FirebaseApp.initializeApp(this);
//        FirebaseMessaging.getInstance().setAutoInitEnabled(true);
//
//        // Manually fetch FCM token to ensure it's working
//        FirebaseMessaging.getInstance().getToken()
//                .addOnCompleteListener(task -> {
//                    if (!task.isSuccessful()) {
//                        Log.w(TAG, "Fetching FCM registration token failed", task.getException());
//                        return;
//                    }
//
//                    // Get new FCM registration token
//                    String token = task.getResult();
//                    Log.d(TAG, "Manual Token: " + token);
//                });
    }
}
