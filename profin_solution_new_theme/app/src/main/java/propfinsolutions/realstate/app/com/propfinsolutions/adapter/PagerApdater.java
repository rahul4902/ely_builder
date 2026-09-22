package propfinsolutions.realstate.app.com.propfinsolutions.adapter;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.fragment.app.FragmentActivity;
import androidx.fragment.app.FragmentManager;
import androidx.lifecycle.Lifecycle;
import androidx.viewpager2.adapter.FragmentStateAdapter;

import java.util.ArrayList;

import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.BookingDoneFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.DashboardFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.FailedFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.HomeFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.InterestedFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.MeetingDoneFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.NewFragment;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments.VisitDoneFragment;

public class PagerApdater extends FragmentStateAdapter {
    public ArrayList<String> page;

    public PagerApdater(@NonNull FragmentManager fragmentManager, @NonNull Lifecycle lifecycle) {
        super(fragmentManager, lifecycle);
        page = new ArrayList<>();
        page.add("Home");
        page.add("New");
        page.add("Interested");
        page.add("Meetings done");
        page.add("Visit done");
        page.add("Booking done");
//        page.add("Failed");
    }

    @NonNull
    @Override
    public Fragment createFragment(int position) {
        switch (position) {
            case 0:
                return new DashboardFragment();
            case 1:
                 return new NewFragment();
            case 2:
                 return new InterestedFragment();
            case 3:
                return new MeetingDoneFragment();
            case 4:
                return new VisitDoneFragment();
            case 5:
                return new BookingDoneFragment();
//            case 6:
//                return new FailedFragment();
            default:
                throw new IllegalStateException("Unexpected position " + position);
        }
    }

    @Override
    public int getItemCount() {
        return page.size();
    }
}
