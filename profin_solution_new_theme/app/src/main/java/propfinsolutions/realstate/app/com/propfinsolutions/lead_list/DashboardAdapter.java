package propfinsolutions.realstate.app.com.propfinsolutions.lead_list;

import android.content.Context;
import android.graphics.Color;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.ProgressBar;
import android.widget.RelativeLayout;
import android.widget.TextView;

import androidx.cardview.widget.CardView;
import androidx.recyclerview.widget.RecyclerView;



import java.util.ArrayList;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModel;

public class DashboardAdapter extends RecyclerView.Adapter<DashboardAdapter.ViewHolder> {
    ArrayList<DataModel> mValues;
    Context mContext;
    protected ItemListener mListener;
    public DashboardAdapter(Context context, ArrayList<DataModel> values, ItemListener itemListener) {
        mValues = values;
        mContext = context;
        mListener=itemListener;
    }
    public class ViewHolder extends RecyclerView.ViewHolder implements View.OnClickListener {
        public TextView textView,title;
        public ImageView imageView;
        public RelativeLayout relativeLayout,rlMoreInfo;

        CardView cardTopLayout;
/*
        ProgressBar pbTotal;
*/
        DataModel item;
        public ViewHolder(View v) {
            super(v);
            v.setOnClickListener(this);
            textView = (TextView) v.findViewById(R.id.tvTotal);
            title = (TextView) v.findViewById(R.id.title);
//            imageView = (ImageView) v.findViewById(R.id.image);
            cardTopLayout = (CardView) v.findViewById(R.id.cardTopLayout);
//            rlMoreInfo = (RelativeLayout) v.findViewById(R.id.rlMoreInfo);
/*
            pbTotal=(ProgressBar)v.findViewById(R.id.pbTotal);
*/
        }
            public void setData(DataModel item) {
            this.item = item;

            textView.setText(String.valueOf(item.count));
            title.setText(item.text);
//            imageView.setImageResource(item.drawable);
            cardTopLayout.setCardBackgroundColor(Color.parseColor(item.color));
          //  rlMoreInfo.setBackgroundColor(Color.parseColor(item.color));
        }
        @Override
        public void onClick(View view) {
            if (mListener != null) {
                mListener.onItemClick(item);
            }
        }
    }
    @Override
    public DashboardAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {

        View view = LayoutInflater.from(mContext).inflate(R.layout.lead_list_count, parent, false);
        return new ViewHolder(view);
    }
    @Override
    public void onBindViewHolder(ViewHolder Vholder, int position) {
        Vholder.setData(mValues.get(position));
    }
    @Override
    public int getItemCount() {

        return mValues.size();
    }

    public interface ItemListener {
        void onItemClick(DataModel item);
    }
}