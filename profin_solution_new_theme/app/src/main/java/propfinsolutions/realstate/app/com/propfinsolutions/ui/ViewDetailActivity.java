package propfinsolutions.realstate.app.com.propfinsolutions.ui;

import android.content.Intent;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.WindowManager;
import android.widget.TextView;

import androidx.activity.OnBackPressedCallback;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import propfinsolutions.realstate.app.com.propfinsolutions.view_detail.ViewDetailFragment;

public class ViewDetailActivity extends AppCompatActivity {

    int pos = 0;
    private TextView username;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen
        setContentView(R.layout.activity_view_detail);

//        username=(TextView)findViewById(R.id.userName);
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
            toolbarTitle.setText("View Details"); // Set custom title
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


        Bundle posBundle = getIntent().getExtras();
        pos = posBundle.getInt("num");

         Bundle bundle = new Bundle();

         bundle.putInt("pos", pos);

         String UserName = new SharedPrefClass(getApplicationContext()).getUserName().toString();

//        username.setText(UserName);

        FragmentManager fragmentManager = getSupportFragmentManager();
        FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
        ViewDetailFragment viewDetailFragment = new ViewDetailFragment();
        viewDetailFragment.setArguments(bundle);
        fragmentTransaction.add(R.id.idViewDetailContainer, viewDetailFragment);
        fragmentTransaction.commit();
    }

    @Override
    public void onBackPressed() {
        super.onBackPressed();
        Intent intent = new Intent(ViewDetailActivity.this, HomeActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);
    }
}
