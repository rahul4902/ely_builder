package propfinsolutions.realstate.app.com.propfinsolutions.lead_list;

import android.content.Context;
import android.graphics.Color;
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
import propfinsolutions.realstate.app.com.propfinsolutions.models.DataModelStatus;

public class TodayListStatusAdapter extends RecyclerView.Adapter<TodayListStatusAdapter.ViewHolder> {
    ArrayList<DataModelStatus> mValues;
    Context mContext;
    protected ItemListener mListener;
    public TodayListStatusAdapter(Context context, ArrayList<DataModelStatus> values, ItemListener itemListener) {
        mValues = values;
        mContext = context;
        mListener=itemListener;
        }
    public class ViewHolder extends RecyclerView.ViewHolder implements View.OnClickListener {
        public TextView textView,title;
//        public ImageView imageView;
        public RelativeLayout relativeLayout;

        CardView cardView;
        DataModelStatus item;
        public ViewHolder(View v) {
            super(v);
            v.setOnClickListener(this);
            textView = (TextView) v.findViewById(R.id.tvTotal);
            title = (TextView) v.findViewById(R.id.title);
//            imageView = (ImageView) v.findViewById(R.id.image);
            cardView = (CardView) v.findViewById(R.id.cardView);

        }
            public void setData(DataModelStatus item) {
            this.item = item;

            textView.setText(String.valueOf(item.count));
            title.setText(item.text);
//            imageView.setImageResource(item.drawable);
                cardView.setCardBackgroundColor(Color.parseColor(item.color));
        }

        @Override
        public void onClick(View view) {
            if (mListener != null) {
                mListener.onItemClick(item);
            }
        }
    }

    @Override
    public TodayListStatusAdapter.ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = LayoutInflater.from(mContext).inflate(R.layout.lead_list_today_count, parent, false);
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
        void onItemClick(DataModelStatus item);
    }
}