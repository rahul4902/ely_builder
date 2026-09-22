package propfinsolutions.realstate.app.com.propfinsolutions.ui.fragments;

import android.app.AlertDialog;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;

import androidx.fragment.app.Fragment;

import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Button;
import android.widget.TextView;

import propfinsolutions.realstate.app.com.propfinsolutions.R;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.NotificationSoundSettingActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.SplashActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.SharedPrefClass;

public class ProfileFragment extends Fragment {
    private TextView tvUserName;
    private String key1;
    private String userName;
    private TextView logoutButton, notification;

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // Inflate the layout for this fragment
        View view = inflater.inflate(R.layout.fragment_profile, container, false);

        tvUserName = view.findViewById(R.id.tv_user_name);
//        logoutButton = view.findViewById(R.id.btn_logout);
        notification = view.findViewById(R.id.notificationSetting);

        key1 = new SharedPrefClass(requireActivity().getApplicationContext()).getSimpleKey();
        userName = new SharedPrefClass(requireActivity().getApplicationContext()).getUserName();
        tvUserName.setText(userName);

        Log.v("key","key1 : "+key1);

//        logoutButton.setOnClickListener(new View.OnClickListener() {
//            @Override
//            public void onClick(View v) {
//                // Create an AlertDialog
//                new AlertDialog.Builder(requireActivity())
//                        .setTitle("Logout Confirmation")
//                        .setMessage("Are you sure you want to logout?")
//                        .setPositiveButton("Yes", new DialogInterface.OnClickListener() {
//                            @Override
//                            public void onClick(DialogInterface dialog, int which) {
//                                // User clicked Yes button, perform logout
//                                final SharedPreferences SpyAppData = requireActivity().getApplicationContext().getSharedPreferences(requireActivity().getApplicationContext().getPackageName(), 0);
//                                SharedPreferences.Editor editor = SpyAppData.edit();
//                                editor.clear();
//                                editor.commit();
//
//                                new SharedPrefClass(requireActivity().getApplicationContext()).logoutUser();
//                                Intent intent = new Intent(requireActivity(), SplashActivity.class);
//                                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_CLEAR_TASK | Intent.FLAG_ACTIVITY_NEW_TASK);
//                                startActivity(intent);
//                            }
//                        })
//                        .setNegativeButton("Cancel", new DialogInterface.OnClickListener() {
//                            @Override
//                            public void onClick(DialogInterface dialog, int which) {
//                                // User clicked Cancel button, dismiss the dialog
//                                dialog.dismiss();
//                            }
//                        })
//                        .show();
//            }
//        });

        notification.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View v) {
                Intent intent=new Intent(requireActivity(), NotificationSoundSettingActivity.class);
                startActivity(intent);
            }
        });
        return view;
    }
}