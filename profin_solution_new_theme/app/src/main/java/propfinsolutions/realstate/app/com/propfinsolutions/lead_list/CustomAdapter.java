package propfinsolutions.realstate.app.com.propfinsolutions.lead_list;

/**
 * Created by Divakar on 7/10/2017.
 */


import android.app.Activity;
import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.Context;
import android.graphics.Color;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.Window;
import android.view.WindowManager;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.BaseAdapter;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import androidx.core.content.ContextCompat;

import java.util.ArrayList;
import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.master.MasterLeadModel;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.LeadStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.MeetingStatus;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.PreviousFollowup;
import propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail.Seniors;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiClient;
import propfinsolutions.realstate.app.com.propfinsolutions.parsingAdapter.ApiInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import retrofit2.Call;
import retrofit2.Callback;
import retrofit2.Response;

/**
 * Created by Divakar on 6/26/2017.
 */

public class CustomAdapter extends BaseAdapter {

    private static final String TAG = CustomAdapter.class.getSimpleName();
    Context mContext;
    List<PreviousFollowup> previousFollowupList;
    String[] mKeys;
    LayoutInflater inflater;
    TextView tv1, tv2,tv11,tv22,tv3,tv33;
    String key1;
    String senior_Spinner,lead_status_position,meeting_status_type;
    List<String> list = new ArrayList<String>();
    List<String> leadstatus_list = new ArrayList<String>();
    List<String> meeting_statustype = new ArrayList<String>();
    EditText entercomment;
    public CustomAdapter(Context mContext, List<PreviousFollowup> previousFollowupList)
    {
        this.mContext = mContext;
        this.previousFollowupList = previousFollowupList;

        inflater = LayoutInflater.from(mContext);
    }
    @Override
    public int getCount() {
        return previousFollowupList.size();
    }

    @Override
    public Object getItem(int position) {
        return previousFollowupList.get(position);
    }

    @Override
    public long getItemId(int position) {
        return position;
    }

    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
         if (convertView == null) {
            LayoutInflater inflater = LayoutInflater.from(parent.getContext());
            convertView = inflater.inflate(R.layout.text, parent, false);
             key1 = new SharedPrefClass(mContext).getSimpleKey().toString();
        }
        tv1 = (TextView)convertView.findViewById(R.id.tv1);
        tv2 = (TextView)convertView.findViewById(R.id.tv2);
        tv11 = (TextView)convertView.findViewById(R.id.tv11);
        tv22 = (TextView)convertView.findViewById(R.id.tv22);
        tv3 = (TextView)convertView.findViewById(R.id.tv3);
        tv33 = (TextView)convertView.findViewById(R.id.tv33);
        String number=String.valueOf(position+1);
        try {
            tv1.setText("Follow Up -"+number);
            String Date=previousFollowupList.get(position).getFollowUpDate();

            String[] separated = Date.split("-");

            String[] separatednew = separated[2].split(" ");
//            Log.v("separated","separated"+ separatednew[1]);

            tv11.setText(separatednew[0]+"-"+separated[1]+"-"+separated[0] + " " +separatednew[1]);
            tv2.setText("Comment -"+number);
            tv22.setText(previousFollowupList.get(position).getComment());
            tv3.setText("Meeting -"+number);
            String meetiingDate=previousFollowupList.get(position).getMeeting_date();
            if(meetiingDate !=null){
                String[] meetiing_Date = meetiingDate.split("-");

                String[] time_date = meetiing_Date[2].split(" ");
//                tv33.setText(meetiingDate);
                tv33.setText(time_date[0]+"-"+meetiing_Date[1]+"-"+meetiing_Date[0] + " " +time_date[1]);
            }else{
                tv33.setText("Meeting Done");
//                tv33.setBackgroundResource(R.drawable.button_gradient);
                tv33.setTextColor(ContextCompat.getColor(parent.getContext(), R.color.green_color));
            }
            if ((tv33.getText().equals("Meeting Done"))){
                tv33.setOnClickListener(new View.OnClickListener() {
                    @Override
                    public void onClick(View v) {

                        final Dialog dialog = new Dialog(mContext, R.style.DialogTheme);
                        dialog.requestWindowFeature(Window.FEATURE_NO_TITLE);
                        dialog.setContentView(R.layout.dialog_layout_meeting);
                        dialog.getWindow().setGravity(Gravity.CENTER);
                        dialog.getWindow().setLayout(WindowManager.LayoutParams.MATCH_PARENT, WindowManager.LayoutParams.WRAP_CONTENT);
                        Spinner senior_list = (Spinner) dialog.findViewById(R.id.senior_list);
                        Spinner lead_status = (Spinner) dialog.findViewById(R.id.lead_status);
                        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
                        int status = new SharedPrefClass(mContext).getLeadType();
//                        Log.v("error","Response"+status);

                        Call<Seniors> call = apiInterface.seniors_list(key1,status);
                        call.enqueue(new Callback<Seniors>() {
                            @Override
                            public void onResponse(Call<Seniors> call, Response<Seniors> response) {
                                try {

                                    list.add("Select Seniors");
                                    Seniors seniors=response.body();
                                    for (Integer i = 0; i <seniors.data.size() ; i++ ) {
                                        list.add(seniors.data.get(i).name);
//                                        Log.v("error","Response"+seniors.data.get(i).name);
                                        ArrayAdapter<String> data_Adapter = new ArrayAdapter<String>(mContext, android.R.layout.simple_spinner_item, list);
                                        data_Adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                                        senior_list.setAdapter(data_Adapter);
                                    }


                                }catch (Exception ee){
                                    ee.printStackTrace();
                                }
                            }
                            @Override
                            public void onFailure(Call<Seniors> call, Throwable t) {
                                Log.v("error","Response"+t.getMessage());

                            }
                      });

                        senior_list.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                            @Override
                            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                                if("Select Seniors".equals(adapterView.getItemAtPosition(i).toString())){
                                }else {
                                    senior_Spinner = adapterView.getItemAtPosition(i).toString();

                                    Log.v("senior_Spinner","senior_Spinner"+senior_Spinner);
                                }
                            }

                            @Override
                            public void onNothingSelected(AdapterView<?> adapterView) {

                            }
                        });


                        Call<LeadStatus> leadstatus = apiInterface.lead_status_response(key1,status);
                        leadstatus.enqueue(new Callback<LeadStatus>() {
                            @Override
                            public void onResponse(Call<LeadStatus> leadstatus, Response<LeadStatus> response) {
                                try {

                                    leadstatus_list.add("Select Lead Status");
                                    LeadStatus seniors=response.body();
                                    for (Integer i = 0; i <seniors.data.size() ; i++ ) {
                                        leadstatus_list.add(seniors.data.get(i).status);
//                                        Log.v("error","Response"+seniors.data.get(i).name);
                                        ArrayAdapter<String> lead_statusadapter = new ArrayAdapter<String>(mContext, android.R.layout.simple_spinner_item, leadstatus_list);
                                        lead_statusadapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                                        lead_status.setAdapter(lead_statusadapter);
                                    }

                                }catch (Exception ee){
                                    ee.printStackTrace();
                                }
                            }
                            @Override
                            public void onFailure(Call<LeadStatus> leadstatus, Throwable t) {
                                Log.v("error","Response"+t.getMessage());

                            }
                        });

                        lead_status.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                            @Override
                            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                                if("Select Lead Status".equals(adapterView.getItemAtPosition(i).toString())){
                                }else {
                                    lead_status_position = adapterView.getItemAtPosition(i).toString();

                                    Log.v("senior_Spinner","senior_Spinner"+lead_status_position);
                                }
                            }

                            @Override
                            public void onNothingSelected(AdapterView<?> adapterView) {

                            }
                        });



                        Spinner meeting_Type = (Spinner) dialog.findViewById(R.id.meeting_Type);
                        Call<MeetingStatus> meeting = apiInterface.meeting_type_status(key1,status);
                        meeting.enqueue(new Callback<MeetingStatus>() {
                            @Override
                            public void onResponse(Call<MeetingStatus> meeting, Response<MeetingStatus> response) {
                                try {
                                    meeting_statustype.add("Select Meeting type");
                                    MeetingStatus seniors=response.body();
                                    for (Integer i = 0; i <seniors.data.size() ; i++ ) {
                                        meeting_statustype.add(seniors.data.get(i).type);
//                                        Log.v("error","Response"+seniors.data.get(i).name);
                                        ArrayAdapter<String> dataAdapter = new ArrayAdapter<String>(mContext, android.R.layout.simple_spinner_item, meeting_statustype);
                                        dataAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                                        meeting_Type.setAdapter(dataAdapter);
                                    }

                                }catch (Exception ee){
                                    ee.printStackTrace();
                                }
                            }
                            @Override
                            public void onFailure(Call<MeetingStatus> meeting, Throwable t) {
                                Log.v("error","Response"+t.getMessage());

                            }
                        });

                        meeting_Type.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                            @Override
                            public void onItemSelected(AdapterView<?> adapterView, View view, int i, long l) {
                                if("Select Meeting type".equals(adapterView.getItemAtPosition(i).toString())){
                                }else {
                                    meeting_status_type = adapterView.getItemAtPosition(i).toString();
                                    Log.v("senior_Spinner","senior_Spinner"+meeting_status_type);
                                }
                            }
                            @Override
                            public void onNothingSelected(AdapterView<?> adapterView) {
                            }
                        });
                        entercomment=(EditText)dialog.findViewById(R.id.entercomment);
                        Button submit=(Button)dialog.findViewById(R.id.submit);

                        submit.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {
                            String id=previousFollowupList.get(position).getId();
                            callmettingapi(key1,id);
                            }
                        });

                        ImageView close = (ImageView) dialog.findViewById(R.id.close);
                        close.setOnClickListener(new View.OnClickListener() {
                            @Override
                            public void onClick(View view) {
                                dialog.dismiss();
                            }
                        });

                        dialog.show();
                        dialog.setCancelable(false);

                    }
                });
            }
        }catch (NullPointerException npe)
        {
            Log.e(TAG, " Error : "+npe, npe);
        }

        return convertView;
         }
     public void callmettingapi(String key,String foolow_id){
      final  ProgressDialog pf=new ProgressDialog(mContext);
        pf.setMessage("Please wait");
        pf.show();
        ApiInterface apiInterface = ApiClient.getClient().create(ApiInterface.class);
//      int status = new SharedPrefClass(mContext).getLeadType();
        apiInterface.meeting(key,entercomment.getText().toString(),senior_Spinner,meeting_status_type,lead_status_position,foolow_id).enqueue(new Callback<MasterLeadModel>() {
            @Override
            public void onResponse(Call<MasterLeadModel> call, Response<MasterLeadModel> response) {
                try {
                    if (response.isSuccessful() && response.code() == 200) {
                        ((Activity)mContext).finish();
                    }
                }catch (Exception e){
                    e.printStackTrace();
                }
                pf.dismiss();
            }

            @Override
            public void onFailure(Call<MasterLeadModel> call, Throwable t) {
                    pf.dismiss();
                Toast.makeText(mContext,"Server error",Toast.LENGTH_LONG).show();
            }
        });
    }
}
