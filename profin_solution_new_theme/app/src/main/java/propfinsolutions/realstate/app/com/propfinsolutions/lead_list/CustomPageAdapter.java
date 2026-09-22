package propfinsolutions.realstate.app.com.propfinsolutions.lead_list;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import java.util.ArrayList;
import java.util.List;
import java.util.Locale;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.LeadList.LeadListPassingModel;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.FollowUpActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewDetailActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;
import propfinsolutions.realstate.app.com.propfinsolutions.view_detail.ViewDetailFragment;

/**
 * Created by Divakar on 6/23/2017.
 */

public class CustomPageAdapter extends RecyclerView.Adapter<CustomPageAdapter.DataViewHolder> {

    Context mContext;
   // String[] title, name, email, source, location, project, budget, requirement, country, state, city, Pincode;
    LayoutInflater inflater;
    List<LeadListPassingModel> arraylist;

    ArrayList<LeadListPassingModel> passLeadListAL = new ArrayList<>();
    CustomPageAdapter(Context mContext, ArrayList<LeadListPassingModel> passLeadListAL)
    {
        this.mContext = mContext;
       this.passLeadListAL = passLeadListAL;
        inflater = LayoutInflater.from(mContext);
        arraylist = new ArrayList<>();
        arraylist.addAll(passLeadListAL);
    }

    class DataViewHolder extends RecyclerView.ViewHolder
    {
        TextView mTitle, mName, mEmail, mSource, mLocation, mProject, mBudget, mRequirement, mFollowUp,status,mMobile;
        Button btnViewDeatil, btnFollowup;
        LinearLayout mailLayout;
        public DataViewHolder(View itemView) {
            super(itemView);
            //TEXT
            mTitle = (TextView)itemView.findViewById(R.id.title);
            mMobile = (TextView)itemView.findViewById(R.id.idMobile);
            status = (TextView)itemView.findViewById(R.id.status);
            mName = (TextView)itemView.findViewById(R.id.idName);
            mEmail = (TextView)itemView.findViewById(R.id.idEmail);
            mSource = (TextView)itemView.findViewById(R.id.idSource);
            mLocation = (TextView)itemView.findViewById(R.id.idLocation);
            mProject = (TextView)itemView.findViewById(R.id.idProject);
            mBudget = (TextView)itemView.findViewById(R.id.idBudget);
            mRequirement = (TextView)itemView.findViewById(R.id.idRequirement);
            mFollowUp = (TextView)itemView.findViewById(R.id.idNxtFollowUp);
            //BUTTON
            btnViewDeatil = (Button)itemView.findViewById(R.id.btnViewDeatil) ;
            btnFollowup = (Button)itemView.findViewById(R.id.btnFollowup);
            mailLayout=(LinearLayout)itemView.findViewById(R.id.mailLayout);

        }
    }

    @Override
    public DataViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View itemView = inflater.inflate(R.layout.fragment_custom_lead, parent, false);
        DataViewHolder dataViewHolder = new DataViewHolder(itemView);
        return dataViewHolder;
    }

    @Override
    public void onBindViewHolder(final DataViewHolder holder, final int position) {

        holder.mMobile.setText(passLeadListAL.get(position).getMobile());
        holder.mTitle.setText("LEAD - "+ passLeadListAL.get(position).getLeadId());
        holder.mName.setText(passLeadListAL.get(position).getName());
        String Email=passLeadListAL.get(position).getEmail().trim();
        if(Email!=null||!Email.isEmpty()) {
            holder.mEmail.setText(Email);
        }else {
            holder.mailLayout.setVisibility(View.GONE);
        }
        holder.mSource.setText(passLeadListAL.get(position).getSource());
        holder.mLocation.setText(passLeadListAL.get(position).getLocation());
        holder.mProject.setText(passLeadListAL.get(position).getProject());
        holder.mBudget.setText(passLeadListAL.get(position).getBudget());
        holder.mRequirement.setText(passLeadListAL.get(position).getRequirement());
        holder.mFollowUp.setText(passLeadListAL.get(position).getNextFollowUp());
        holder.status.setText(passLeadListAL.get(position).getStatus());
      final   String Status=passLeadListAL.get(position).getStatus();
        holder.btnViewDeatil.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
             //   Toast.makeText(mContext, "Count : "+position, Toast.LENGTH_LONG).show();
                int leadID = Integer.parseInt(passLeadListAL.get(position).getLeadId());
                Intent intent=new Intent(mContext,ViewDetailFragment.class);
                SharedPreferences pref=mContext.getSharedPreferences("Status",Context.MODE_PRIVATE);
                SharedPreferences.Editor editor=pref.edit();
                editor.putString("Status",Status);
                editor.commit();
                new SharedPrefClass(holder.btnFollowup.getContext()).setLeadID(passLeadListAL.get(position).getLeadId());
                Navigatior.getClassInstance().navigateToActivityWithDataNotAnim(holder.btnViewDeatil.getContext(), ViewDetailActivity.class, null, leadID);

            }
        });
        int status = new SharedPrefClass(mContext).getLeadType();
        if(status==2){
            holder.btnFollowup.setVisibility(View.GONE);
        }

        if(Status.equalsIgnoreCase("close")){
            holder.btnFollowup.setVisibility(View.GONE);
        }
        else {
            holder.btnFollowup.setVisibility(View.VISIBLE);
        }
        holder.btnFollowup.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {

                //passLeadListAL.get(position).getLeadId();
                new SharedPrefClass(holder.btnFollowup.getContext()).setLeadID(passLeadListAL.get(position).getLeadId());
                Navigatior.getClassInstance().navigateToActivityWithContext(holder.btnFollowup.getContext(), FollowUpActivity.class);

            }
        });

    }

    @Override
    public int getItemCount() {
       // return title.length;
        return passLeadListAL.size();
    }

    public void filter(String charText) {

        charText = charText.toLowerCase(Locale.getDefault());

        passLeadListAL.clear();
        if (charText.length() == 0) {
            passLeadListAL.addAll(arraylist);

        } else {
            for (LeadListPassingModel postDetail : arraylist) {
                String shop=postDetail.getNextFollowUp();
                if(shop!=null)
                    if (charText.length() != 0 && postDetail.getNextFollowUp().toLowerCase(Locale.getDefault()).contains(charText)) {
                        passLeadListAL.add(postDetail);
                } else if (charText.length() != 0 && postDetail.getMobile().toLowerCase(Locale.getDefault()).contains(charText)) {
                        passLeadListAL.add(postDetail);
                }
                    else if (charText.length() != 0 && postDetail.getName().toLowerCase(Locale.getDefault()).contains(charText)) {
                        passLeadListAL.add(postDetail);
                    }
                    }
            }
            notifyDataSetChanged();
        }

    }

