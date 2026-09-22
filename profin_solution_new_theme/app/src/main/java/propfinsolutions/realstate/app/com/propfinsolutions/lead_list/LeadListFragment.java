package propfinsolutions.realstate.app.com.propfinsolutions.lead_list;

import android.app.Fragment;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.EditText;

import androidx.annotation.Nullable;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadList;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListPassingModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class LeadListFragment extends Fragment {
    private static final String TAG = LeadListFragment.class.getSimpleName();
    View itemView;
    // DECLARATION
    RecyclerView mrc1;
    CustomPageAdapter adapter;

    String title = "Lead";
    ArrayList<LeadListPassingModel> passLeadListAL = new ArrayList<>();

     String lead_id = "NA";
    String name ="NA";
    String email ="NA";
    String source ="NA";
    String location ="NA";
    String project ="NA";
    String budget ="NA";
    String requirement ="NA";
    String nextFollowUp ="NA";
    String Status ="NA";
    String mobile ="NA";

    DialogClass dialogClass;
    private EditText dateSearch;
    private String Date;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        itemView = inflater.inflate(R.layout.fragment_list_lead, container, false);

        mrc1 = (RecyclerView) itemView.findViewById(R.id.rc1);
        dateSearch=(EditText)itemView.findViewById(R.id.date);

        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(itemView.getContext(), LinearLayoutManager.VERTICAL, false);
        mrc1.setLayoutManager(mLayoutManager);

        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(itemView.getContext());
        // GETTING DATA
        String key1 = new SharedPrefClass(itemView.getContext()).getSimpleKey();
        Log.v("key1","key---"+key1);
        int status = new SharedPrefClass(itemView.getContext()).getLeadType();
        Log.v("key1","key---"+status);
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
        apiInterface.getLeadList(key1, status).enqueue(new Callback<LeadListModel>() {
            @Override
            public void onResponse(Call<LeadListModel> call, Response<LeadListModel> response) {

                if(response.isSuccessful()){
                    try {
                        dialogClass.stopProgress();
                        List<LeadList> recordLeadList = response.body().getLeadList();
                        for (int i=0; i<recordLeadList.size(); i++) {

                            if(recordLeadList.get(i).getId() != null)
                            {
                                lead_id = recordLeadList.get(i).getId();}
                            if(recordLeadList.get(i).getName() != null)
                            {  name = recordLeadList.get(i).getName();}
                            if(recordLeadList.get(i).getEmail() != null)
                            {  email = recordLeadList.get(i).getEmail();}
                            if(recordLeadList.get(i).getSource() != null)
                            {  source = recordLeadList.get(i).getSource();}

                            if(recordLeadList.get(i).getMobile()!=null){
                                mobile=recordLeadList.get(i).getMobile();
                            }
                            if(recordLeadList.get(i).getLocation() != null)
                            {  location = recordLeadList.get(i).getLocation();}
                            if(recordLeadList.get(i).getProject() != null)
                            {  project = recordLeadList.get(i).getProject();}
                            if(recordLeadList.get(i).getBudget() != null)
                            {  budget = recordLeadList.get(i).getBudget();}
                            if(recordLeadList.get(i).getRequirement() != null)
                            {  requirement = recordLeadList.get(i).getRequirement();}
                            if(recordLeadList.get(i).getNextFollowUp() != null)
                            {  nextFollowUp = recordLeadList.get(i).getNextFollowUp();}
                            if(recordLeadList.get(i).getStatus() != null)
                            {   Status= recordLeadList.get(i).getStatus();}
                            System.out.println(nextFollowUp);

                            LeadListPassingModel leadListPassingModel = new LeadListPassingModel(mobile,lead_id, name, email, source, location, project, budget, requirement,Status, nextFollowUp);
                            passLeadListAL.add(leadListPassingModel);
                        }
                          adapter = new CustomPageAdapter(itemView.getContext(), passLeadListAL);
                        mrc1.setAdapter(adapter);
                    }catch (NullPointerException npe)
                    {
                        Log.e(TAG, "Error  : "+npe,npe);
                    }

                }else{
                    getActivity().finish();
                }
            }

            @Override
            public void onFailure(Call<LeadListModel> call, Throwable t) {

            }
        });

        dateSearch.addTextChangedListener(new TextWatcher() {
            @Override
            public void beforeTextChanged(CharSequence charSequence, int i, int i1, int i2) {

            }

            @Override
            public void onTextChanged(CharSequence charSequence, int i, int i1, int i2) {
                adapter.filter(charSequence.toString().trim().toLowerCase());
                mrc1.invalidate();
            }
            @Override
            public void afterTextChanged(Editable editable) {

            }
        });
        return itemView;
    }

    @Override
    public void onViewCreated(View view, @Nullable Bundle savedInstanceState) {
        super.onViewCreated(view, savedInstanceState);

       /* mrc1 = (RecyclerView) itemView.findViewById(R.id.rc1);

        RecyclerView.LayoutManager mLayoutManager = new LinearLayoutManager(itemView.getContext(), LinearLayoutManager.VERTICAL, false);
        mrc1.setLayoutManager(mLayoutManager);
        adapter = new CustomPageAdapter(itemView.getContext(), title, name, email, source, location, project, budget, requirement, country, state, city, Pincode);
        mrc1.setAdapter(adapter);*/

    }

}
