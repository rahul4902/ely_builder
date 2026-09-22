package propfinsolutions.realstate.app.com.propfinsolutions.new_lead;

import android.app.Fragment;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Spinner;
import android.widget.SpinnerAdapter;
import android.widget.Toast;

import androidx.annotation.Nullable;

import java.util.List;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.master.Budget;
import propfinsolutions.realstate.app.com.propfinsolutions.master.MasterLeadModel;
import propfinsolutions.realstate.app.com.propfinsolutions.master.Project;
import propfinsolutions.realstate.app.com.propfinsolutions.master.Requirement;
import propfinsolutions.realstate.app.com.propfinsolutions.master.Source;
import propfinsolutions.realstate.app.com.propfinsolutions.models.newlead.NewLead;
import propfinsolutions.realstate.app.com.propfinsolutions.models.newlead.NewLeadModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.HomeActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Created by Divakar on 7/14/2017.
 */

public class NewLeadFragment extends Fragment {

    @BindView(R.id.idETName)
    EditText nameET;
    @BindView(R.id.idETContactNo)
    EditText contactNoET;
    @BindView(R.id.idETEmail)
    EditText emailET;
    @BindView(R.id.idETCity)
    EditText cityET;
    @BindView(R.id.idETState)
    EditText stateET;
    @BindView(R.id.idETCountry)
    EditText countryET;
    @BindView(R.id.idETPincode)
    EditText pincodeET;
    @BindView(R.id.idSPNSource)
    Spinner sourceSPN;
    @BindView(R.id.idETLocations)
    EditText locationsET;
    @BindView(R.id.idSPNProject)
    Spinner projectSPN;
    @BindView(R.id.other_project)
    EditText customProjectET;
    @BindView(R.id.other_requirement)
    EditText customRequirementET;

    @BindView(R.id.idSPNRequirement)
    Spinner requirementSPN;
    @BindView(R.id.idSPNLeadType)
    Spinner leadTypeSPN;
    @BindView(R.id.idSPNBudget)
    Spinner budgetSPN;

    @BindView(R.id.idBtnSubmit)
    Button submitBTN;
    // DATA
   // String[] source = {"Personal", "Reference", "99 Acre","Other"};
    String[] location = {"Delhi", "Noida", "GuruGram", "Ghaziabad", "Faridabad", "Manesar"};
  //  String[] project = {"Parsvanath", "Gaur City", "DLF", "Omaxe", "Emr MGF", "Raheja Developer"};
 //   String[] requirement = {"1 BHK", "2 BHK", "3 BHK"};
    String[] status = {"Hot","Cold"};
 //   String[] budget = {"1.49Lac - 3Lac Rs.", "3Lac - 7.49Lac Rs.", "7.5Lac - 13Lac Rs.","Other"};

    View newLeadView;

    String source="";
    String project= "";
    String requirement="";
    String leadtype= "";
    String budget= "";
    DialogClass dialogClass;
    private EditText other_source,other_requirement,other_budge,other_project;
    //
    List<Project> projectList;
    List<Requirement> RequirementList;
    String OtherSource,OtherRequirement,OtherBudge,OtherProject;

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        newLeadView = inflater.inflate(R.layout.fragment_new_lead, container, false);

        ButterKnife.bind(this, newLeadView);
        other_budge=(EditText)newLeadView.findViewById(R.id.other_budge);
        other_requirement=(EditText)newLeadView.findViewById(R.id.other_requirement);
        other_source=(EditText)newLeadView.findViewById(R.id.other_source);
        other_project=(EditText)newLeadView.findViewById(R.id.other_project);

        return newLeadView;
    }

    @Override
    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

        // MASTER API END
        try {
            spinnerCount();
        }catch (Exception e){
            e.printStackTrace();
        }

       // setOutputSource(source);
      //  setOutputProject(project);
     //   setOutputRequirement(requirement);
         setOutputStatus(status);
       // setOutputBudget(budget);

                 sourceSPN.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
        @Override
        public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
            if(sourceSPN.getSelectedItem().equals("Other")){
                other_source.setVisibility(View.VISIBLE);
            }
            if(!(sourceSPN.getSelectedItem().equals("Other"))){
                other_source.setVisibility(View.GONE);
            }

        }

        @Override
        public void onNothingSelected(AdapterView<?> parent) {

        }
    });



         budgetSPN.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if(budgetSPN.getSelectedItem().equals("Other")){
                    other_budge.setVisibility(View.VISIBLE);
                 }
             if(!(budgetSPN.getSelectedItem().equals("Other"))){
                 other_budge.setVisibility(View.GONE);
             }

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });


        requirementSPN.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if(requirementSPN.getSelectedItem().equals("Other")){
                    other_requirement.setVisibility(View.VISIBLE);

                }
              if(!(requirementSPN.getSelectedItem().equals("Other"))){
                  other_requirement.setVisibility(View.GONE);
              }

            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });
        projectSPN.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                if(projectSPN.getSelectedItem().equals("Other")){
                    other_project.setVisibility(View.VISIBLE);
                }
                if(!(projectSPN.getSelectedItem().equals("Other"))){
                    other_project.setVisibility(View.GONE);
                }
            }

            @Override
            public void onNothingSelected(AdapterView<?> parent) {

            }
        });

    }
    @OnClick(R.id.idBtnSubmit)
    public void submit()
    {
        //  Toast.makeText(NewLeadActivity.this, "Clicked", Toast.LENGTH_LONG).show();
        inputOutputData();
    }


    // SPN
    public void setOutputSource(List<Source> sourceList)
    {
        SpinnerSourceAdapter customAdapter1 = new SpinnerSourceAdapter(newLeadView.getContext(), sourceList);
        sourceSPN.setAdapter(customAdapter1);
    }

    public void setOutputProjectList(List<Project> projectList)
    {

        SpinnerProjectCustomAdapter customAdapter = new SpinnerProjectCustomAdapter(newLeadView.getContext(), projectList);
        projectSPN.setAdapter(customAdapter);
    }
    public void setOutputRequirementList(List<Requirement> requirementList)
    {
        SpinnerRequirementCustomAdapter customAdapter1 = new SpinnerRequirementCustomAdapter(newLeadView.getContext(), requirementList);
        requirementSPN.setAdapter(customAdapter1);
    }


    public void setOutputStatus(String[] status)
    {
        SpinnerAdapter sourceAdapter = new ArrayAdapter<>(newLeadView.getContext(), R.layout.text_spn_lead, status);
        leadTypeSPN.setAdapter(sourceAdapter);
    }
    public void setOutputBudget(List<Budget> budgetList)
    {
        SpinnerBudgetAdapter customAdapter1 = new SpinnerBudgetAdapter(newLeadView.getContext(), budgetList);
        budgetSPN.setAdapter(customAdapter1);
    }

    public void spinnerCount(){
        String key1 = new SharedPrefClass(newLeadView.getContext()).getSimpleKey();
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getLeadMaster(key1).enqueue(new Callback<MasterLeadModel>() {
            @Override
            public void onResponse(Call<MasterLeadModel> call, Response<MasterLeadModel> response) {

                List<Project> projectList = response.body().getProject();
                for(int i = 0; i<projectList.size(); i++)
                {
                    System.out.println(projectList.get(i).getProjectName());
                    System.out.println(projectList.get(i).getProjectName());
                }

                List<Requirement> RequirementList = response.body().getRequirement();
                List<Source> SourceList=response.body().getSource();
                List<Budget> BudgetList=response.body().getBudget();

                for(int i = 0; i<RequirementList.size(); i++)
                {
                    System.out.println(RequirementList.get(i).getRequirementName());
                    System.out.println(RequirementList.get(i).getRequirementName());

                }
                setOutputProjectList(projectList);
                setOutputRequirementList(RequirementList);
                setOutputBudget(BudgetList);
                setOutputSource(SourceList);
            }

            @Override
            public void onFailure(Call<MasterLeadModel> call, Throwable t) {
            }
        });
    }
    public boolean inputOutputData()
    {
        String key1 = new SharedPrefClass(newLeadView.getContext()).getSimpleKey();
        String name = nameET.getText().toString();
        String contactNo = contactNoET.getText().toString();
        String email = emailET.getText().toString();
        String city = cityET.getText().toString();
        final String state = stateET.getText().toString();
        String country = countryET.getText().toString();
        String pincode = pincodeET.getText().toString();

        String location = locationsET.getText().toString();
        if(source != null && sourceSPN.getSelectedItem() !=null ) {
            source = (String)sourceSPN.getSelectedItem();
        } else  {

        }
        if(project != null && projectSPN.getSelectedItem() !=null ) {
            project = (String)projectSPN.getSelectedItem();
        } else  {

        }   if(requirement != null && requirementSPN.getSelectedItem() !=null ) {
        requirement = (String)requirementSPN.getSelectedItem();
        } else  {

        }
       if(leadtype != null && leadTypeSPN.getSelectedItem() !=null ) {
           leadtype = (String)leadTypeSPN.getSelectedItem();
        } else  {

        }

        if(budget != null && budgetSPN.getSelectedItem() !=null ) {
            budget = (String)budgetSPN.getSelectedItem();
        } else  {

        }





//
//        String location = locationsET.getText().toString();
//        String project = projectSPN.getSelectedItem().toString();
//        final String requirement = requirementSPN.getSelectedItem().toString();
//        String leadtype = leadTypeSPN.getSelectedItem().toString();
//        String budget = budgetSPN.getSelectedItem().toString();

        if(name.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your Name!",Toast.LENGTH_SHORT).show();
            return false;
        }
        if(contactNo.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your Contact No.",Toast.LENGTH_SHORT).show();
            return false;
        }
        if(contactNo.length()!=10){
            Toast.makeText(getActivity(),"Invalid Mobile Number!",Toast.LENGTH_SHORT).show();
            return false;
        }
/*
        if(!(bussiness.getSelectedItem().equals("Shop")
                ||bussiness.getSelectedItem().equals("Restaurant")||bussiness.getSelectedItem().equals("Other"))){
            Toast.makeText(this,"Business Type can not be empty!",Toast.LENGTH_SHORT).show();
            return false;
        }
*/
    /*    if(sourceSPN.getSelectedItem().equals("Other")){
            source=other_source.getText().toString().trim();
        }
        if(projectSPN.getSelectedItem().equals("Other")){
            project=other_project.getText().toString().trim();

        }
        if(requirementSPN.getSelectedItem().equals("Other")){
            OtherRequirement=other_requirement.getText().toString().trim();

        }
        if(budgetSPN.getSelectedItem().equals("Other")){
            budget=other_budge.getText().toString().trim();
        }
*/
        if(email.isEmpty()) {
            Toast.makeText(getActivity(), "Enter Your Email", Toast.LENGTH_SHORT).show();
            return false;
        }
        if(city.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your City",Toast.LENGTH_SHORT).show();
            return false;
        }
         if(state.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your State",Toast.LENGTH_SHORT).show();
            return false;
        }
        if(country.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your Country Name",Toast.LENGTH_SHORT).show();
            return false;
        }
/*
        if(pincode.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your Pin code",Toast.LENGTH_SHORT).show();
            return false;
        }
*/
        if(location.isEmpty()){
            Toast.makeText(getActivity(),"Enter Your Location",Toast.LENGTH_SHORT).show();
            return false;
        }
        else {

            callAddNewLeadApi(key1,name,contactNo,email,country,state,city,pincode,location,source,project,requirement,budget,leadtype);
            Log.v("key1","key1"+key1);
            Log.v("contactNo","contactNo"+contactNo);
            Log.v("country","email"+email);
            Log.v("email","email"+email);
            Log.v("state","state"+state);
            Log.v("city","city"+city);
            Log.v("pincode","pincode"+pincode);
            Log.v("requirement","requirement"+requirement);
            Log.v("location","location"+location);
            Log.v("budget","budget"+location);
            Log.v("leadtype","leadtype"+location);
        }

    //   Toast.makeText(newLeadView.getContext(), "Output : "+source, Toast.LENGTH_LONG).show();
    return true;
    }
    public void callAddNewLeadApi(String key,String name,String contactNo,String email,String country,String state,String city,String pincode,String location,String source,String project,String requirement,String budget,String leadtype){
        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(newLeadView.getContext());
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getNewLead(key,name,contactNo,email, country, state, city, pincode, location, source, project, requirement, budget, leadtype).enqueue(new Callback<NewLeadModel>() {
            @Override
            public void onResponse(Call<NewLeadModel> call, Response<NewLeadModel> response) {
                if(response.isSuccessful() && response.code() == 200)
                {
                    dialogClass.stopProgress();
                    List<NewLead> newLeadResponse = response.body().getNewLead();
                    Log.v("newLeadResponse","newLeadResponse"+newLeadResponse);
                    //   Integer status = newLeadResponse.get(0).getStatus();
                    Toast.makeText(newLeadView.getContext(), "Lead Successfully Added", Toast.LENGTH_LONG).show();
                    getActivity().startActivity(new Intent(getActivity(), HomeActivity.class).addFlags(Intent.FLAG_ACTIVITY_NO_ANIMATION));
                }else {
                }
            }
            @Override
            public void onFailure(Call<NewLeadModel> call, Throwable t) {
            }
        });
    }
}
