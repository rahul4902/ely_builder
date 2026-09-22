package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import android.os.Bundle;

import androidx.annotation.NonNull;
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
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.CallLogsAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.adapter.NotificationAdapter;
import propfinsolutions.realstate.app.com.propfinsolutions.models.calllog.GetCallLogsModelResponse;
import propfinsolutions.realstate.app.com.propfinsolutions.models.notification.NotificationResponseModel;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.DialogClass;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.PaginationHelper;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

public class CallLogsFragment extends Fragment {

    CallLogsAdapter callLogsAdapter;
    private LinearLayout llNoDataFound;
    DialogClass dialogClass;
    private String key1;
    ArrayList<GetCallLogsModelResponse.Calllogs> callList;
    private LinearLayoutManager layoutManager;
    private boolean loading = true;
    RecyclerView callLogsRecycler;
    private int pastVisibleItem, visibleItemCount, totalItemCount;


    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_call_logs, container, false);
        callLogsRecycler = view.findViewById(R.id.callLogsRecycler);
        llNoDataFound = view.findViewById(R.id.llNoDataFound);

        dialogClass = DialogClass.getInstance();
        dialogClass.startProgress(requireActivity());
        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        Log.v("key","key1 : "+key1);

        layoutManager = new LinearLayoutManager(requireActivity());
        callLogsRecycler.setLayoutManager(layoutManager);
//
        callUserCallLogApi(key1);

        // Set up pagination
        PaginationHelper.setUpPagination(callLogsRecycler, layoutManager, loading, () -> {
            // Logic to load more data
            Toast.makeText(requireActivity().getApplicationContext(), "Loading more data...", Toast.LENGTH_SHORT).show();

            // Call your API to load more data
            callUserCallLogApi(key1);

            // Notify adapter
            callLogsAdapter.notifyDataSetChanged();

            // Reset loading flag
            loading = true;
        });

        //setUpPagination();
        return view;
    }
    @Override
    public void onResume() {
        super.onResume();
       // callUserCallLogApi(key1);
    }

//    private void setUpPagination(){
//        callLogsRecycler.addOnScrollListener(new RecyclerView.OnScrollListener() {
//            @Override
//            public void onScrollStateChanged(@NonNull RecyclerView recyclerView, int newState) {
//                super.onScrollStateChanged(recyclerView, newState);
//            }
//
//            @Override
//            public void onScrolled(@NonNull RecyclerView recyclerView, int dx, int dy) {
//                super.onScrolled(recyclerView, dx, dy);
//                if (dy > 0){
//                    visibleItemCount = layoutManager.getChildCount();
//                    totalItemCount = layoutManager.getItemCount();
//                    pastVisibleItem = layoutManager.findFirstVisibleItemPosition();
//                    if (loading){
//                        if ((visibleItemCount + pastVisibleItem) >= totalItemCount){
//                            loading = false;
//                            Toast.makeText(requireActivity().getApplicationContext(), "not more data.", Toast.LENGTH_SHORT).show();
//
//                            callUserCallLogApi(key1);
//                            callLogsAdapter.notifyDataSetChanged();  // Notify the adapter of the data change
//                            loading = true;
//                        }
//                    }
//                }
//            }
//        });
//    }

    public void callUserCallLogApi(String key) {
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);

        Log.v("newKey", "key : " + key);
        apiInterface.getCallLogs(key).enqueue(new Callback<GetCallLogsModelResponse>() {
            @Override
            public void onResponse(Call<GetCallLogsModelResponse> call, Response<GetCallLogsModelResponse> response) {
                try {
                    if (response.isSuccessful() && response.body() != null) {
                        List<GetCallLogsModelResponse.Calllogs> list = response.body().getCalllogs();

                        if (list == null || list.isEmpty()) {
                            llNoDataFound.setVisibility(View.VISIBLE);
                            callLogsRecycler.setVisibility(View.GONE);
                        } else {
                            llNoDataFound.setVisibility(View.GONE);
                            callLogsRecycler.setVisibility(View.VISIBLE);
                            callList = new ArrayList<>();
                            // Clear the old data and add new data
                            callList.clear();
                            callList.addAll(list);

                            callLogsAdapter = new CallLogsAdapter(requireContext().getApplicationContext(), callList);
                            callLogsRecycler.setAdapter(callLogsAdapter);
                            callLogsAdapter.notifyDataSetChanged();  // Notify the adapter of the data change
                        }
                    } else {
                        llNoDataFound.setVisibility(View.VISIBLE);
                        callLogsRecycler.setVisibility(View.GONE);
                    }

                }catch(Exception e){
                    Log.d("Call log", "failed : "+ e.getMessage());
                }
                dialogClass.stopProgress();
            }
            @Override
            public void onFailure(Call<GetCallLogsModelResponse> call, Throwable t) {
                dialogClass.stopProgress();
                // Handle failure (e.g., show a Toast or log the error)
                Log.d("onFailure", "failed : "+ t.getMessage());
            }
        });
    }
}