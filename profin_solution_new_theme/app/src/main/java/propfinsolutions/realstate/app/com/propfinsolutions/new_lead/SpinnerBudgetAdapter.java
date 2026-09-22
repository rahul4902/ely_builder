package propfinsolutions.realstate.app.com.propfinsolutions.new_lead;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import java.util.ArrayList;
import java.util.List;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.master.Budget;

/**
 * Created by Divakar on 7/15/2017.
 */

public class SpinnerBudgetAdapter extends BaseAdapter{

    Context _context;
    List<Budget> requirementList = new ArrayList<>();
    LayoutInflater inflater;
    SpinnerBudgetAdapter(Context _context, List<Budget> requirementList)
    {
        this._context = _context;
        this.requirementList = requirementList;
        inflater = LayoutInflater.from(_context);
    }
    @Override
    public int getCount() {
        return requirementList.size();
    }

    @Override
    public Object getItem(int position) {
        return requirementList.get(position).getBudget_range();
    }

    @Override
    public long getItemId(int position) {
        return 0;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
        View itemView = inflater.inflate(R.layout.text_spn_lead, parent, false);

        TextView tv1 = (TextView)itemView.findViewById(R.id.idSpnText);
        tv1.setText(requirementList.get(position).getBudget_range());

        return itemView;
    }
}
