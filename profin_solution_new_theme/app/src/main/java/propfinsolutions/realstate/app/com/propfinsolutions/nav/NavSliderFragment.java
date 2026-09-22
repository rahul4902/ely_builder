package propfinsolutions.realstate.app.com.propfinsolutions.nav;


import android.app.Fragment;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.Button;
import android.widget.ListView;
import android.widget.SlidingDrawer;
import android.widget.Toast;

import androidx.annotation.Nullable;

import butterknife.BindView;
import butterknife.ButterKnife;
import butterknife.OnItemClick;
import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.HomeActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.LeadListActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.NewLeadActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.PushNotificationActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.SplashActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.Navigatior;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;

/**
 * Created by Divakar on 6/23/2017.
 */


public class NavSliderFragment extends Fragment {
    //INIT
    @BindView(R.id.simpleSlidingDrawer)
    SlidingDrawer simpleSlidingDrawer ;
    @BindView(R.id.handle)
    Button handleButton ;
    @BindView(R.id.simpleListView)
    ListView simpleListView ;
    View navView;String[] nameArray = {"Home", "New Lead", "Total Lead", "Open Lead", "Closed Lead", "Log Out"};
    @Nullable
    @Override
    public View onCreateView(LayoutInflater inflater, @Nullable ViewGroup container, @Nullable Bundle savedInstanceState) {
        navView = inflater.inflate(R.layout.fragment_nav_slider, container, false);
        ButterKnife.bind(this, navView);
        /*SlidingDrawer simpleSlidingDrawer = (SlidingDrawer) navView.findViewById(R.id.simpleSlidingDrawer);
        final Button handleButton = (Button) navView.findViewById(R.id.handle);
        ListView simpleListView = (ListView) navView.findViewById(R.id.simpleListView);*/
        ArrayAdapter<String> arrayAdapter = new ArrayAdapter<String>(navView.getContext(), R.layout.side_menu_item, R.id.name, nameArray);
        simpleListView.setAdapter(arrayAdapter);
        simpleSlidingDrawer.setOnDrawerOpenListener(new SlidingDrawer.OnDrawerOpenListener() {
            @Override
            public void onDrawerOpened() {
                handleButton.setText("Close");
                handleButton.setBackgroundResource(R.color.app_color);

            }
        });
        simpleSlidingDrawer.setOnDrawerCloseListener(new SlidingDrawer.OnDrawerCloseListener() {
            @Override
            public void onDrawerClosed() {
                handleButton.setText("Open");
                handleButton.setBackgroundResource(R.color.app_color_ts);
            }
        });
        return navView;
    }
    @OnItemClick(R.id.simpleListView)
    public void OnItemClick(int position)
    {
       // Toast.makeText(navView.getContext(), "Clicked"+position+"\n"+simpleListView.getItemAtPosition(position), Toast.LENGTH_LONG).show();
        connectView(simpleListView.getItemAtPosition(position).toString());
    }

    public void connectView(String selectedview)
    {
        switch (selectedview)
        {
            case "Home":
            {
                Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), HomeActivity.class);
                break;
            }
            case "Push":
            {
                Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), PushNotificationActivity.class);
                break;
            }
            case "New Lead":
            {
                Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), NewLeadActivity.class);
                break;
            }
            case "Total Lead":
            {
                Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), LeadListActivity.class);
                new SharedPrefClass(navView.getContext()).setLeadType(3);
                break;
            }
            case "Open Lead":
            {
                Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), LeadListActivity.class);
                new SharedPrefClass(navView.getContext()).setLeadType(1);
                break;
            }
            case "Closed Lead":
            {
                Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), LeadListActivity.class);
                new SharedPrefClass(navView.getContext()).setLeadType(2);
                break;
            }
            case "Log Out":
            {
                final SharedPreferences SpyAppData = navView.getContext().getSharedPreferences(navView.getContext().getPackageName(), 0);
                SharedPreferences.Editor editor = SpyAppData.edit();
                editor.clear();
                editor.commit();

                new SharedPrefClass(navView.getContext()).logoutUser();
                Intent intent=new Intent(navView.getContext(),SplashActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_NEW_TASK);
                startActivity(intent);
                //Navigatior.getClassInstance().navigateToActivityWithContext(navView.getContext(), SplashActivity.class);
                break;
            }

            default:
            {
                Toast.makeText(navView.getContext(), "Nothing Found", Toast.LENGTH_LONG).show();
                break;
            }
        }
    }
}
