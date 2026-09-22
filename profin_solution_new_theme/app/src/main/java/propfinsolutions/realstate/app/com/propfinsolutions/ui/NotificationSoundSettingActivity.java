package propfinsolutions.realstate.app.com.propfinsolutions.ui;

import android.content.Intent;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.ImageView;
import android.widget.SeekBar;
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

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.AppController;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.ConnectivityReceiver;

public class NotificationSoundSettingActivity extends AppCompatActivity {
    SeekBar seekBar;
    ImageView back_btn;
    TextView title;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        EdgeToEdge.enable(this);

        setContentView(R.layout.activity_notification_sound_setting);
        ViewCompat.setOnApplyWindowInsetsListener(findViewById(R.id.main), (v, insets) -> {
            Insets systemBars = insets.getInsets(WindowInsetsCompat.Type.systemBars());
            v.setPadding(systemBars.left, systemBars.top, systemBars.right, systemBars.bottom);
            return insets;
        });

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

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
            toolbarTitle.setText("Notification setting"); // Set custom title
        }

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



        seekBar=findViewById(R.id.volumeSeekBar);

//        back_btn = (ImageView)findViewById(R.id.back_btn);
//        title = (TextView) findViewById(R.id.title);
//        title.setText(R.string.notification);

//        back_btn.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//
//                if(ConnectivityReceiver.isConnected()){
///*
//                    Intent i = new Intent(NotificationSoundSettingActivity.this, Dashboard.class);
//                    startActivity(i);*/
//                    onBackPressed();
//                }else {
//                    Toast.makeText(NotificationSoundSettingActivity.this, "Please check internet connection",Toast.LENGTH_LONG).show();
//                }
//
//            }
//        });

        seekBar.setProgress(AppController.getInstance().getVolume());

        seekBar.setOnSeekBarChangeListener(new SeekBar.OnSeekBarChangeListener() {
            @Override
            public void onProgressChanged(SeekBar seekBar, int progress, boolean fromUser) {
                AppController.getInstance().setVolume(progress);
                Log.d("dddd", "onProgressChanged: "+progress);
            }

            @Override
            public void onStartTrackingTouch(SeekBar seekBar) {

            }

            @Override
            public void onStopTrackingTouch(SeekBar seekBar) {

            }
        });
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        finish();
    }
}