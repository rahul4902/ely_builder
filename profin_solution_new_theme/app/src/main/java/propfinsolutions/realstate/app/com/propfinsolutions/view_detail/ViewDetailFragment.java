package propfinsolutions.realstate.app.com.propfinsolutions.view_detail;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import java.util.LinkedHashMap;
import java.util.List;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.CustomAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.DetailRecord;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.PrevFollowupModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.PreviousFollowup;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.ViewDetailModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.FollowUpActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewEditDetails;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class ViewDetailFragment extends Fragment {
    View detailView;
    TextView tvDateTime;
    ImageView followUpEditIV,editdetails;
    int leadID;
    String key1,Status;
    //HashMap<String, String> listMap = new HashMap<>();
    LinkedHashMap<String, String> listMap = new LinkedHashMap<>();
    LinkedHashMap<String, String> followupLHM = new LinkedHashMap<String, String>();
    ListView list_first, list_second;
    LinearLayout main;
    DialogClass dialogClass;
    private TextView name,email,source,location,project,budget,requirement,country,state,city,pincode,mobile;
    private LinearLayout followuplayout,mailLayout,proedit;
    SharedPreferences pref;
    private void setOutputNextFollowUp(String strDateTime)
    {
        tvDateTime.setText(strDateTime);
    }

    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        detailView = inflater.inflate(R.layout.fragment_view_detail, container, false);
        Bundle bundle = getArguments();
        pref=getActivity().getSharedPreferences("Status", Context.MODE_PRIVATE);
        Status=pref.getString("Status","");
        leadID = bundle.getInt("pos");
        //Log.v("leadID","leadID"+leadID);
        new SharedPrefClass(getContext()).setLead_ID(String.valueOf(leadID));
        return detailView;
    }

    @Override
    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);
       // list_first = (ListView) detailView.findViewById(R.id.first_listview);
        list_second = (ListView) detailView.findViewById(R.id.secondList);

      //  main=(LinearLayout)detailView.findViewById(R.id.main);


        // API INTEGRATION START
        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(detailView.getContext());
         key1 = new SharedPrefClass(detailView.getContext()).getSimpleKey().toString();

        try {
            calldetailApi(key1,leadID);
        }catch (Exception e){
            e.printStackTrace();
        }

        callLeadFollowapi(key1,leadID);
        LayoutInflater inflater = getActivity().getLayoutInflater();
        ViewGroup header = (ViewGroup)inflater.inflate(R.layout.header_view,list_second,false);
        name = (TextView)header.findViewById(R.id.name);
        mobile = (TextView)header.findViewById(R.id.mobile);
        email = (TextView)header.findViewById(R.id.email);
        source = (TextView)header.findViewById(R.id.source);
        location = (TextView)header.findViewById(R.id.location);
        project = (TextView)header.findViewById(R.id.project);
        budget = (TextView)header.findViewById(R.id.budget);
        requirement = (TextView)header.findViewById(R.id.requirement);
        country = (TextView)header.findViewById(R.id.country);
        state = (TextView)header.findViewById(R.id.state);
        city = (TextView)header.findViewById(R.id.city);
        pincode = (TextView)header.findViewById(R.id.pincode);
        tvDateTime = (TextView)header.findViewById(R.id.tvDateTime);
        followUpEditIV = (ImageView)header.findViewById(R.id.idFollowUpEdit);
//        proedit = (LinearLayout)header.findViewById(R.id.proedit);
        followuplayout=(LinearLayout)header.findViewById(R.id.followuplayout);
        mailLayout=(LinearLayout)header.findViewById(R.id.mailLayout);
        list_second.addHeaderView(header);
        // EDIT CLICK
        int status = new SharedPrefClass(getActivity()).getLeadType();
       if(status==2){
           followuplayout.setVisibility(View.GONE);
         }
         if(Status.equalsIgnoreCase("close")){
             followuplayout.setVisibility(View.GONE);
         }
        followUpEditIV.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Navigatior.getClassInstance().navigateToActivity(getActivity(), FollowUpActivity.class);
            }
        });


//        proedit.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                Intent intent=new Intent(getContext(),ViewEditDetails.class);
//                startActivity(intent);
//            }
//        });


    }

 public void calldetailApi(String key,int leadid){
    ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
    apiInterface.getDetailView(key, leadid).enqueue(new Callback<ViewDetailModel>() {
        @Override
        public void onResponse(Call<ViewDetailModel> call, Response<ViewDetailModel> response) {
            try {


                if (response.isSuccessful() && response.code() == 200) {
                    dialogClass.stopProgress();
                    List<DetailRecord> detailRecordList = response.body().getDetailRecord();
                    // main.setVisibility(View.VISIBLE);

                    String Name = detailRecordList.get(0).getName();
                    if (!Name.isEmpty() || Name != null) {
                        name.setText(Name);
                    }
                    String Mobile=detailRecordList.get(0).getMobile();
                    if(Mobile!=null&&!Mobile.isEmpty()){
                        mobile.setText(Mobile);
                    }
                    String Email = detailRecordList.get(0).getEmail().trim();
                    if ( Email != null||!Email.isEmpty()) {
                        email.setText(Email);
                    }if(Email==null||Email.isEmpty()) {
                        mailLayout.setVisibility(View.GONE);
                    }

                    String Source = detailRecordList.get(0).getSource().toString().trim();
                    if (!Source.isEmpty() || Source != null) {
                        source.setText(Source);
                    }

                    String Location = detailRecordList.get(0).getLocation();
                    if (!Location.isEmpty() || Location != null) {
                        location.setText(Location);
                    }

                    String Lroject = detailRecordList.get(0).getProject();
                    if (!Lroject.isEmpty() || Lroject != null) {
                        project.setText(Lroject);
                    }

                    String Budget = detailRecordList.get(0).getBudget();
                    if (!Budget.isEmpty() || Budget != null) {
                        budget.setText(Budget);
                    }

                    String Requirement = detailRecordList.get(0).getRequirement();
                    if (!Requirement.isEmpty() || Requirement != null) {
                        requirement.setText(Requirement);
                    }

                    String Country = detailRecordList.get(0).getCountry();
                    if (!Country.isEmpty() || Country != null) {
                        country.setText(Country);
                    }

                    String State = detailRecordList.get(0).getState();
                    if (!State.isEmpty() || State != null) {
                        state.setText(State);
                    }

                    String City = detailRecordList.get(0).getCity();
                    if (!City.isEmpty() || City != null) {
                        city.setText(City);
                    }

                    String Pincode = detailRecordList.get(0).getPincode();
                    if (!Pincode.isEmpty() || Pincode != null) {
                        pincode.setText(Pincode);
                    }

                    //
                    String nextFollowUp = detailRecordList.get(1).getFollowUpDate();

                    System.out.println(name);
                    setOutputNextFollowUp(nextFollowUp);

         /*        listMap.put("name",name);
                listMap.put("email",email);
                listMap.put("source",source);
                listMap.put("location",location);
                listMap.put("project",project);
                listMap.put("budget",budget);
                listMap.put("requirement",requirement);
                listMap.put("country",country);
                listMap.put("state",state);
                listMap.put("city",city);
                listMap.put("Pincode",Pincode);
                CustomAdapter adapter = new CustomAdapter(detailView.getContext(), listMap);
                list_first.setAdapter(adapter);
                ListUtils.setDynamicHeight(list_first);*/
                    //setAdapter(listMap);

                }
            }catch (Exception e){
                e.printStackTrace();
            }
        }

        @Override
        public void onFailure(Call<ViewDetailModel> call, Throwable t) {

        }
    });

}
    public void callLeadFollowapi(String key,int leadid){
//        Log.v("Key","Key"+key);
//        Log.v("Key","leadid"+leadid);
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getDetailPreviousLead(key, leadid).enqueue(new Callback<PrevFollowupModel>() {
            @Override
            public void onResponse(Call<PrevFollowupModel> call, Response<PrevFollowupModel> response) {
                try {
                    if (response.isSuccessful() && response.code() == 200) {
                      final List<PreviousFollowup>  previousFollowupList = response.body().getPreviousFollowup();
                        if (previousFollowupList.equals(null)){
                            list_second.setAdapter(null);
                        }
                        if (previousFollowupList.size() > 0) {
                            CustomAdapter adapter1 = new CustomAdapter(detailView.getContext(), previousFollowupList);
                            list_second.setAdapter(adapter1);
                            // list_second.setNestedScrollingEnabled(true);
                           // ListUtils.setListViewHeightBasedOnChildren(list_second);
                        }else {
                            list_second.setAdapter(null);
                        }

                    }
                }catch (Exception e){
                    e.printStackTrace();
                }
            }

            @Override
            public void onFailure(Call<PrevFollowupModel> call, Throwable t) {

            }
        });
    }

    @Override
    public void onResume() {
        super.onResume();
        callLeadFollowapi(key1,leadID);
    }
}
