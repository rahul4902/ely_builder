package propfinsolutions.realstate.app.com.propfinsolutions.ui;
import android.app.ProgressDialog;
import android.content.Intent;
import android.graphics.PorterDuff;
import android.graphics.drawable.Drawable;
import android.os.Bundle;

import androidx.activity.OnBackPressedCallback;
import androidx.appcompat.app.AppCompatActivity;
import androidx.appcompat.widget.Toolbar;
import androidx.core.content.ContextCompat;

import android.view.View;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.SpinnerAdapter;
import android.widget.TextView;
import android.widget.Toast;

import java.util.List;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.login.LoginModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.DetailRecord;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.ViewDetailModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ViewEditDetails extends AppCompatActivity implements View.OnClickListener{
    EditText inputName,inputEmail,inputMobile,inputSource,inputLocation,inputBugget,requirement,inputCountary,inputCity,inputState,inputPincode,inputProject,leattype;
    private TextView username;
    String key;
    String leadID;
    Button btnSubmit;
    String[] status = {"Hot","Cold"};
    Spinner leadTypeSPN;
    String lead_type;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        this.getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN,
                WindowManager.LayoutParams.FLAG_FULLSCREEN);
        setContentView(R.layout.a_view_edit_details);

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
            toolbarTitle.setText("View Detail"); // Set custom title
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

        key = new SharedPrefClass(ViewEditDetails.this).getSimpleKey();


        Intent getIntent = getIntent();
        if(getIntent!=null){
            leadID = getIntent.getStringExtra("leadid");
        }
        initViews();
        setOutputStatus(status);


    }

    private void initViews() {
//        username=(TextView)findViewById(R.id.userName);
        inputName=findViewById(R.id.inputName);
        inputEmail=findViewById(R.id.inputEmail);
        inputMobile=findViewById(R.id.inputMobile);
        inputSource=findViewById(R.id.inputSource);
        inputLocation=findViewById(R.id.inputLocation);
        inputProject=findViewById(R.id.project);
        inputBugget=findViewById(R.id.inputBugget);
        inputCountary=findViewById(R.id.inputCountary);
        inputState=findViewById(R.id.inputState);
        inputCity=findViewById(R.id.inputCity);
        inputPincode=findViewById(R.id.inputPincode);
        requirement=findViewById(R.id.requirement);
        btnSubmit=findViewById(R.id.btnSubmit);
        String UserName = new SharedPrefClass(getApplicationContext()).getUserName().toString();
//        username.setText(UserName);
        calldetailApi();
        btnSubmit.setOnClickListener(this);
        leadTypeSPN=(Spinner)findViewById(R.id.leattype);

        leadTypeSPN.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {

                lead_type=status[position];


            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
    }

    public void setOutputStatus(String[] status)
    {

        SpinnerAdapter sourceAdapter = new ArrayAdapter<>(ViewEditDetails.this, R.layout.text_spn_lead, status);
        leadTypeSPN.setAdapter(sourceAdapter);
    }

    @Override
    public void onClick(View v) {
        switch (v.getId()){
            case R.id.btnSubmit:

//               String email = inputEmail.getText().toString();
                if(inputName.getText().toString().length()==0){
                    Toast.makeText(this,"Please enter the name",Toast.LENGTH_LONG).show();
                }else if(inputEmail.getText().toString().length()==0){
                    Toast.makeText(this,"Please enter the email",Toast.LENGTH_LONG).show();
                }else {
                    callApi();
                }

                break;
        }
    }

    private void callApi() {
        final ProgressDialog mProgressDialog = new ProgressDialog(this);
        mProgressDialog.setIndeterminate(true);
        mProgressDialog.setMessage("Loading...");
        mProgressDialog.setTitle(R.string.app_name);
        mProgressDialog.show();

        String name = inputName.getText().toString();
        String email = inputEmail.getText().toString();
        String state = inputState.getText().toString();
        String city = inputCity.getText().toString();
        String location = inputSource.getText().toString();
        String project = inputProject.getText().toString();
        String Pincode = inputPincode.getText().toString();
        String Budget = inputBugget.getText().toString();
        String update_req = requirement.getText().toString();

        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        apiInterface.leadUpdate(key,Integer.parseInt(new SharedPrefClass(ViewEditDetails.this).getLead_ID()),name,email,state,city,location,project,update_req,Budget,lead_type).enqueue(new Callback<LoginModel>() {
            @Override
            public void onResponse(Call<LoginModel> call, Response<LoginModel> response) {
                mProgressDialog.dismiss();
                Navigatior.getClassInstance().navigateToActivity(ViewEditDetails.this, HomeActivity.class);
            }

            @Override
            public void onFailure(Call<LoginModel> call, Throwable t) {
                mProgressDialog.dismiss();
                Toast.makeText(ViewEditDetails.this, "Opps!!! Server Connection Error", Toast.LENGTH_LONG).show();
            }
        });


    }
    public void calldetailApi() {
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getDetailView(key, Integer.parseInt(new SharedPrefClass(ViewEditDetails.this).getLead_ID())).enqueue(new Callback<ViewDetailModel>() {
            @Override
            public void onResponse(Call<ViewDetailModel> call, Response<ViewDetailModel> response) {
                try {
                    if (response.isSuccessful() && response.code() == 200) {
                        List<DetailRecord> detailRecordList = response.body().getDetailRecord();
                        String Name = detailRecordList.get(0).getName();
                        if (!Name.isEmpty() || Name != null) {
                            inputName.setText(Name);
                        }
                        String Mobile = detailRecordList.get(0).getMobile();
                        if (Mobile != null && !Mobile.isEmpty()) {
                            inputMobile.setText(Mobile);
                        }
                        String Email = detailRecordList.get(0).getEmail().trim();
                        if (Email != null || !Email.isEmpty()) {
                            inputEmail.setText(Email);
                        }

                        String Source = detailRecordList.get(0).getSource().toString().trim();
                        if (!Source.isEmpty() || Source != null) {
                            inputSource.setText(Source);
                        }

                        String Location = detailRecordList.get(0).getLocation();
                        if (!Location.isEmpty() || Location != null) {
                            inputLocation.setText(Location);
                        }

                        String Lroject = detailRecordList.get(0).getProject();
                        if (!Lroject.isEmpty() || Lroject != null) {
                            inputProject.setText(Lroject);
                        }

                        String Budget = detailRecordList.get(0).getBudget();
                        if (!Budget.isEmpty() || Budget != null) {
                            inputBugget.setText(Budget);
                        }

                        String Requirement = detailRecordList.get(0).getRequirement();
                        if (!Requirement.isEmpty() || Requirement != null) {
                            requirement.setText(Requirement);
                        }

                        String Country = detailRecordList.get(0).getCountry();
                        if (!Country.isEmpty() || Country != null) {
                            inputCountary.setText(Country);
                        }

                        String State = detailRecordList.get(0).getState();
                        if (!State.isEmpty() || State != null) {
                            inputState.setText(State);
                        }

                        String City = detailRecordList.get(0).getCity();
                        if (!City.isEmpty() || City != null) {
                            inputCity.setText(City);
                        }

                        String Pincode = detailRecordList.get(0).getPincode();
                        if (!Pincode.isEmpty() || Pincode != null) {
                            inputPincode.setText(Pincode);
                        }



                    }
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(Call<ViewDetailModel> call, Throwable t) {

            }
        });
    }

    @Override
    public void onBackPressed() {
        Intent intent=new Intent(ViewEditDetails.this,HomeActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_NEW_TASK);
        startActivity(intent);

    }

}
