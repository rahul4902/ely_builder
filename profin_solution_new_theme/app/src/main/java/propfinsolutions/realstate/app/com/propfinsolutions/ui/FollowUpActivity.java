package propfinsolutions.realstate.app.com.propfinsolutions.ui;

import android.app.Dialog;
import android.app.TimePickerDialog;
import android.content.Intent;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.DatePicker;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.TimePicker;
import android.widget.Toast;

import androidx.activity.OnBackPressedCallback;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.DialogFragment;

import java.text.DateFormat;
import java.text.SimpleDateFormat;
import java.util.Calendar;
import java.util.List;
import java.util.Locale;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.newfollowup.NewFollowup;
import propfinsolutions.realstate.app.com.propfinsolutions.models.newfollowup.NewFollowupModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class FollowUpActivity extends AppCompatActivity implements View.OnClickListener {

    private ImageView timeIV, dateIV;
    private static final String TIME_PATTERN = "HH:mm";
    static TextView lblDate;
    private TextView lblTime;
    private int mYear, mMonth, mDay, mHour, mMinute;

    private Calendar calendar;
    private DateFormat dateFormat;
    private SimpleDateFormat timeFormat;
    private EditText commentET;
    LinearLayout datetimeLayout;
    Button submitBTN;
    private String LeadStatus;
    Spinner leadStatusSPN;
    String[] list = {"In Progress Lead", "Close Lead","Number Not Valid","Broker","Not Interested"};
    DialogClass dialogClass;
    private TextView userName;
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        // hide the title bar
        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN); //enable full screen
        setContentView(R.layout.activity_follow_up);

        Toolbar toolbar = findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);

        // Enable the back arrow in the toolbar
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
            toolbarTitle.setText("Follow Up"); // Set custom title
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


        leadStatusSPN = (Spinner)findViewById(R.id.spnLeadStatus) ;
        timeIV = (ImageView)findViewById(R.id.btnTimePicker) ;
        dateIV = (ImageView) findViewById(R.id.btnDatePicker);
        lblDate = (TextView) findViewById(R.id.lblDate);
        lblTime = (TextView) findViewById(R.id.lblTime);
        commentET = (EditText)findViewById(R.id.idCommentET);
        submitBTN = (Button)findViewById(R.id.idSubmitBTN);
        datetimeLayout=(LinearLayout)findViewById(R.id.datetimeLayout);
//        userName=(TextView)findViewById(R.id.userName);

        String UserName = new SharedPrefClass(FollowUpActivity.this).getUserName().toString();
//        if(UserName!=null) {
//            userName.setText(UserName);
//        }
        timeIV.setOnClickListener(FollowUpActivity.this);
        dateIV.setOnClickListener(FollowUpActivity.this);

        ArrayAdapter<String> al = new ArrayAdapter<>(FollowUpActivity.this, android.R.layout.simple_spinner_dropdown_item, list);
        leadStatusSPN.setAdapter(al);
        leadStatusSPN.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if(leadStatusSPN.getSelectedItem().equals("Close Lead")){
                    datetimeLayout.setVisibility(View.GONE);
                    LeadStatus="2";
                }

                if(leadStatusSPN.getSelectedItem().equals("Number Not Valid") || leadStatusSPN.getSelectedItem().equals("Broker")||leadStatusSPN.getSelectedItem().equals("Not Interested")){
                    datetimeLayout.setVisibility(View.VISIBLE);
                    LeadStatus="5";
                }
                if((leadStatusSPN.getSelectedItem().equals("In Progress Lead"))){
                    datetimeLayout.setVisibility(View.VISIBLE);
                    LeadStatus="4";
                }

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

        // DATE TIME
        calendar = Calendar.getInstance();
        dateFormat = DateFormat.getDateInstance(DateFormat.LONG, Locale.getDefault());
        timeFormat = new SimpleDateFormat(TIME_PATTERN, Locale.getDefault());

        findViewById(R.id.btnDatePicker).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                DialogFragment dFragment = new SelectDateFragment();
                // Show the date picker dialog fragment
                dFragment.show(getSupportFragmentManager(),"Date Picker");
            }
        });

        findViewById(R.id.btnTimePicker).setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                final Calendar c = Calendar.getInstance();
                mHour = c.get(Calendar.HOUR_OF_DAY);
                mMinute = c.get(Calendar.MINUTE);
                TimePickerDialog timePickerDialog = new TimePickerDialog(FollowUpActivity.this,
                        new TimePickerDialog.OnTimeSetListener() {

                            @Override
                            public void onTimeSet(TimePicker view, int hourOfDay,
                                                  int minute) {

                                lblTime.setText(hourOfDay + ":" + minute);
                            }
                        }, mHour, mMinute, false);
                timePickerDialog.show();
            }
        });
        //  update();
        submitBTN.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                validate();
            }
        });
    }

    public boolean validate(){
        String key1 = new SharedPrefClass(FollowUpActivity.this).getSimpleKey().toString();
        // String leadStatus = "7";
        String leadStatus = new SharedPrefClass(FollowUpActivity.this).getLeadID();
        String time = lblTime.getText().toString();
        String date = lblDate.getText().toString();
        String followupDate = date+" "+time;//new Formatter().formatDate(date)+" "+lblTime.getText().toString();

        String comment = commentET.getText().toString();
        if(!(leadStatusSPN.getSelectedItem().equals("Close Lead"))){
            if(time.isEmpty()){
                Toast.makeText(getApplicationContext(),"Please Select Time",Toast.LENGTH_LONG).show();
                return false;
            }
            if(date.isEmpty()){
                Toast.makeText(getApplicationContext(),"Please Select Date",Toast.LENGTH_LONG).show();
                return false;
            }
        }
        if(comment.isEmpty()){
            Toast.makeText(getApplicationContext(),"Please Enter Comments",Toast.LENGTH_LONG).show();
            return false;
        }
        inputOutputData(key1,leadStatus,followupDate,comment,LeadStatus);
        return true;
    }

    private void update() {
        // lblDate.setText(dateFormat.format(calendar.getTime()));
        // lblTime.setText(timeFormat.format(calendar.getTime()));
    }
    public void onClick(View view) {
        switch (view.getId()) {
            case R.id.btnDatePicker:
                // DatePickerDialog.newInstance(this, calendar.get(Calendar.YEAR), calendar.get(Calendar.MONTH), calendar.get(Calendar.DAY_OF_MONTH)).show(getFragmentManager(), "datePicker");
                break;
            case R.id.btnTimePicker:
                break;
        }
    }

    public void inputOutputData(String key,String leadstatus,String followdate,String coments,String status)
    {

        Log.v("Data","data------"+status);
        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(FollowUpActivity.this);
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getNewFollowUp(key, leadstatus, followdate, coments,status).enqueue(new Callback<NewFollowupModel>() {
            @Override
            public void onResponse(Call<NewFollowupModel> call, Response<NewFollowupModel> response) {

                if(response.isSuccessful() && response.code() == 200)
                {
                    if(leadStatusSPN.getSelectedItem().equals("Close Lead")){
                        Toast.makeText(getApplicationContext(),"Lead Closed Successfully",Toast.LENGTH_LONG).show();
                        startActivity(new Intent(getApplicationContext(), HomeActivity.class));
                        finish();
                    }
                    else {
                        dialogClass.stopProgress();
                        List<NewFollowup> newFollowupList = response.body().getNewFollowup();
                        Toast.makeText(FollowUpActivity.this, "Follow Up Successfully Added", Toast.LENGTH_LONG).show();
                        finish();
                    }

                }
            }

            @Override
            public void onFailure(Call<NewFollowupModel> call, Throwable t) {

            }
        });
    }

    public static class SelectDateFragment extends DialogFragment implements android.app.DatePickerDialog.OnDateSetListener {

        @Override
        public Dialog onCreateDialog(Bundle savedInstanceState) {
            final Calendar calendar = Calendar.getInstance();
            int yy = calendar.get(Calendar.YEAR);
            int mm = calendar.get(Calendar.MONTH);
            int dd = calendar.get(Calendar.DAY_OF_MONTH);

            // Create a DatePickerDialog
            android.app.DatePickerDialog datePickerDialog = new android.app.DatePickerDialog(getContext(), this, yy, mm, dd);

            // Set minimum date to current date
            datePickerDialog.getDatePicker().setMinDate(calendar.getTimeInMillis());

            // Set maximum date to 15 days from current date
            calendar.add(Calendar.DAY_OF_MONTH, 15); // Add 15 days
            datePickerDialog.getDatePicker().setMaxDate(calendar.getTimeInMillis());

            return datePickerDialog;


        }

        public void onDateSet(DatePicker view, int yy, int mm, int dd) {
            populateSetDate(yy, mm+1, dd);
        }
        public void populateSetDate(int year, int month, int day) {
            lblDate.setText(year+"-"+month+"-"+day);
        }

    }

}
