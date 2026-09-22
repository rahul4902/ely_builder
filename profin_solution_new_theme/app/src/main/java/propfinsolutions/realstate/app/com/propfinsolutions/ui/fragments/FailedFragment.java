package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.pm.PackageManager;
import android.net.Uri;
import android.os.Bundle;

import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.core.content.ContextCompat;
import androidx.fragment.app.Fragment;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.LinearLayout;
import android.widget.Toast;

import java.util.ArrayList;
import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.interfaces.RecyclerOnClick;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.LeadsFilterAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.LeadListActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewDetailActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import propfinsolutions.realstate.app.com.propfinsolutions.view_detail.ViewDetailFragment;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class FailedFragment extends Fragment implements RecyclerOnClick {
    private RecyclerView newLeadsRecycler;
    private LinearLayout llNoDataFound;
    DialogClass dialogClass;
    private String key1;
    private ArrayList<LeadsFilter.LeadList> arrayList;
    LeadsFilterAdapter adapter;
    private static final String TAG = HomeFragment.class.getSimpleName();

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_failed, container, false);

        newLeadsRecycler = view.findViewById(R.id.failedRecycler);
        llNoDataFound = view.findViewById(R.id.llNoDataFound);

        // Initialize the adapter once, and update it later

        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(requireActivity());
        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        Log.v("key","key1 : "+key1);

        callNewLeadFilterApi(key1, "failed");

        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
        callNewLeadFilterApi(key1, "failed");
    }

    public void callNewLeadFilterApi(String key, String status) {
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.getLeadFilterList(key, status).enqueue(new Callback<LeadsFilter>() {
            @Override
            public void onResponse(Call<LeadsFilter> call, Response<LeadsFilter> response) {

                if (response.isSuccessful() && response.body() != null) {
                    List<LeadsFilter.LeadList> leadList = response.body().getLeadList();

                    if (leadList == null || leadList.isEmpty()) {
                        llNoDataFound.setVisibility(View.VISIBLE);
                        newLeadsRecycler.setVisibility(View.GONE);
                    } else {
                        llNoDataFound.setVisibility(View.GONE);
                        newLeadsRecycler.setVisibility(View.VISIBLE);
                        arrayList = new ArrayList<>();
                        // Clear the old data and add new data
                        arrayList.clear();
                        arrayList.addAll(leadList);

                        LinearLayoutManager layoutManager = new LinearLayoutManager(requireActivity().getApplicationContext());
                        newLeadsRecycler.setLayoutManager(layoutManager);
                        adapter = new LeadsFilterAdapter(requireActivity().getApplicationContext(), arrayList, FailedFragment.this, false);
                        newLeadsRecycler.setAdapter(adapter);
                        adapter.notifyDataSetChanged();  // Notify the adapter of the data change
                    }
                } else {
                    llNoDataFound.setVisibility(View.VISIBLE);
                    newLeadsRecycler.setVisibility(View.GONE);
                }
                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<LeadsFilter> call, Throwable t) {
                dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
            }
        });
    }

    @Override
    public void onItemClick(LeadsFilter.LeadList item) {
        Navigatior.getClassInstance().navigateToActivity(requireActivity(), LeadListActivity.class);
        new SharedPrefClass(requireActivity().getApplicationContext()).setLeadType(3);

    }

    @Override
    public void onItemCalendarClick(LeadsFilter.LeadList item) {
        int leadID = Integer.parseInt(item.getId());
        Intent intent=new Intent(requireActivity(), ViewDetailFragment.class);
        SharedPreferences pref=requireActivity().getApplicationContext().getSharedPreferences("Status", Context.MODE_PRIVATE);
        SharedPreferences.Editor editor=pref.edit();
        editor.putString("Status",item.getStatus());
        editor.commit();
        new SharedPrefClass(requireActivity().getApplicationContext()).setLeadID(item.getId());
        Navigatior.getClassInstance().navigateToActivityWithDataNotAnim(requireActivity(), ViewDetailActivity.class, null, leadID);
    }

    @Override
    public void onItemChatClick(LeadsFilter.LeadList item) {
        Intent intent = new Intent(Intent.ACTION_VIEW);
        intent.setData(Uri.parse("http://api.whatsapp.com/send?phone=" + "+91" + item.getContactNo()));
        startActivity(intent);
    }

    @Override
    public void onItemCallClick(LeadsFilter.LeadList item) {

        // Check if permission is granted
        if (ContextCompat.checkSelfPermission(getContext(), android.Manifest.permission.CALL_PHONE) != PackageManager.PERMISSION_GRANTED) {
            // If permission is not granted, request permission
            ActivityCompat.requestPermissions(getActivity(), new String[]{android.Manifest.permission.CALL_PHONE}, 1);
        } else {
            // Permission granted, initiate the call
            Intent phoneIntent = new Intent(Intent.ACTION_CALL);
            Log.d("phone call number", item.getContactNo());
            phoneIntent.setData(Uri.parse("tel:" + item.getContactNo()));
            startActivity(phoneIntent);
        }
//        Intent phoneIntent = new Intent(Intent.ACTION_CALL);
//
//        // Set data of Intent through Uri by parsing phone number
//        phoneIntent.setData(Uri.parse("tel:" + item.getContactNo()));
//
//        startActivity(phoneIntent);
    }

    @Override
    public void onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        if (requestCode == 1) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                // Permission granted
                Toast.makeText(getContext(), "Permission Granted", Toast.LENGTH_SHORT).show();
            } else {
                // Permission denied
                Toast.makeText(getContext(), "Permission Denied", Toast.LENGTH_SHORT).show();
            }
        }
    }

}