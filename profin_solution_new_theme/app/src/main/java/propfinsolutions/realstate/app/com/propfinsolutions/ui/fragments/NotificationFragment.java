package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import android.os.Bundle;

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
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.NotificationAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.lead_list.LeadsFilterAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.home.LeadsFilter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.PaginationHelper;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class NotificationFragment extends Fragment {
    NotificationAdapter notificationAdapter;
    private LinearLayout llNoDataFound;
    DialogClass dialogClass;
    private String key1;

    private LinearLayoutManager layoutManager;
    private boolean loading = true;
    RecyclerView notifyRecycler;
    ArrayList<NotificationResponseModel.UserNotifications> notifyList;
    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {

        // Inflate the layout for this fragment
        View view =  inflater.inflate(R.layout.fragment_notification, container, false);

        notifyRecycler = view.findViewById(R.id.notifyRecycler);
        llNoDataFound = view.findViewById(R.id.llNoDataFound);

        dialogClass = DialogClass.getInstance();
        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        Log.v("key","key1 : "+key1);

        layoutManager = new LinearLayoutManager(requireActivity());
        notifyRecycler.setLayoutManager(layoutManager);

        callUserNotifyApi(key1);

        PaginationHelper.setUpPagination(notifyRecycler, layoutManager, loading, () -> {
            // Logic to load more data
            Toast.makeText(requireActivity().getApplicationContext(), "Loading more data...", Toast.LENGTH_SHORT).show();

            // Call your API to load more data
            callUserNotifyApi(key1);

            // Notify adapter
            notificationAdapter.notifyDataSetChanged();

            // Reset loading flag
            loading = true;
        });


        return view;
    }

    @Override
    public void onResume() {
        super.onResume();
       // callUserNotifyApi(key1);
    }

    public void callUserNotifyApi(String key) {
        dialogClass.startProgress(requireActivity());
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.getUserNotification(key).enqueue(new Callback<NotificationResponseModel>() {
            @Override
            public void onResponse(Call<NotificationResponseModel> call, Response<NotificationResponseModel> response) {
                try {
                    if (response.isSuccessful() && response.body() != null) {
                        List<NotificationResponseModel.UserNotifications> list = response.body().getUserNotifications();

                        if (list == null || list.isEmpty()) {
                            llNoDataFound.setVisibility(View.VISIBLE);
                            notifyRecycler.setVisibility(View.GONE);
                        } else {
                            llNoDataFound.setVisibility(View.GONE);
                            notifyRecycler.setVisibility(View.VISIBLE);
                            notifyList = new ArrayList<>();
                            // Clear the old data and add new data
                            notifyList.clear();
                            notifyList.addAll(list);

                            notificationAdapter = new NotificationAdapter(requireContext().getApplicationContext(), notifyList);
                            notifyRecycler.setAdapter(notificationAdapter);
                            notificationAdapter.notifyDataSetChanged();  // Notify the adapter of the data change
                        }
                    } else {

                        llNoDataFound.setVisibility(View.VISIBLE);
                        notifyRecycler.setVisibility(View.GONE);
                    }
                }catch (Exception e){
                    Log.d("NotificationFrag", "failed : "+ e.getMessage());
                }

                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<NotificationResponseModel> call, Throwable t) {
                dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
                Log.d("onFailure", "failed : "+ t.getMessage());
            }
        });
    }
}