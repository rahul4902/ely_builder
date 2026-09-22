package propfinsolutions.realstate.app.com.propfinsolutions.utils;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.util.Log;

import java.util.HashMap;
import java.util.Map;

import propfinsolutions.realstate.app.com.propfinsolutions.ui.LoginActivity;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewDetailActivityOne;

/**
 * Created by Divakar on 7/6/2017.
 */

public class SharedPrefClass {

    Context _context;
    private static final String TAG = SharedPrefClass.class.getSimpleName();
    SharedPreferences sharedPreferences ;
    SharedPreferences.Editor editor ;

    // Sharedpref file name
    private static final String PREF_NAME = "ProplinPref";

    private static final String LOGIN_KEY = "key";
    private static final String USER_NAME = "name";
    private static final String USER_LEADID = "leadid";

    private static final String total = "total";
    private static final String open = "openid";
    private static final String progress = "progress";
    private static final String close = "close";
    public static final String PREFS_OTP = "OTP";

    private static final String total_lead_count_postions = "notifications_postion ";

    public SharedPrefClass(Context _context)
    {
        this._context = _context;
    }

    public void createLoginSession(String key)
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString(LOGIN_KEY, key);
        editor.commit();
    }
    public void setUserName(String Name){
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString(USER_NAME, Name);
        editor.commit();
    }
  public void setOtp(String OTP){
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString(PREFS_OTP, OTP);
        editor.commit();
    }

    public void totalLeadDetails(Integer total_lead,Integer open_lead,Integer progress_lead,Integer close_lead){
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putInt(total, total_lead);
        editor.putInt(open, open_lead);
        editor.putInt(progress, progress_lead);
        editor.putInt(close, close_lead);
        editor.commit();
    }



    public int getLotal_lead()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getInt(total, 0);
    }

    public String getOTP()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString(PREFS_OTP, null);
    }
    public int getOpen_lead()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getInt(open, 0);
    }

    public int getProgress_lead()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getInt(progress, 0);
    }

    public int getClose_lead()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getInt(close, 0);
    }






    public void setLead_ID(String leadid){
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString(USER_LEADID, leadid);
        editor.commit();
    }

    public String getLead_ID()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString(USER_LEADID, null);
    }


    public HashMap<String, String> getUserDetails()
    {
        HashMap<String, String> user = new HashMap<>();
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
         sharedPreferences.getString(LOGIN_KEY, null);

        user.put(LOGIN_KEY, sharedPreferences.getString(LOGIN_KEY, null));
        return user;
    }

    public String getSimpleKey()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString(LOGIN_KEY, null);
    }
    public String getUserName()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString(USER_NAME, null);
    }

    public boolean IS_LOGIN()
    {
        boolean staus = false;
        try {
            String value_key = "";
            HashMap<String, String> user = new SharedPrefClass(_context).getUserDetails();
            for (Map.Entry me : user.entrySet()) {
                System.out.println(me.getKey() + " : " + me.getValue());
                value_key = me.getValue().toString();
                if (value_key.length() > 0) {
                    staus = true;
                }
            }
        }catch (NullPointerException npe)
        {
            Log.e(TAG, "#Error : "+npe, npe);
        }
        return staus;
    }

    public void logoutUser(){
        // Clearing all data from Shared Preferences
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.clear();
        editor.commit();

        Intent i = new Intent(_context, LoginActivity.class);
        i.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        _context.startActivity(i);
    }

    public void setLeadType(int type)    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putInt("lead_type", type);
        editor.commit();
    }
    public int getLeadType() {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getInt("lead_type", 0);
    }

    public void setLeadID(String id) {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString("lead_id", id);
        editor.commit();
    }
    public String getLeadID() {
         sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
         return sharedPreferences.getString("lead_id", null);
/*
        Log.v("LEADIDDDDD", "",+sharedPreferences)
*/
    }


    public void totalLeadID(int total) {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString("total_lead_count_postions", total_lead_count_postions);
        editor.commit();

    }

    public String getLeadTotalPostionsID()
    {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString("total_lead_count_postions", null);

/*
        Log.v("LEADIDDDDD", "",+sharedPreferences)
*/
    }

    public void setCheckInDate(String date) {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        editor = sharedPreferences.edit();
        editor.putString("check_in_date", date);
        editor.commit();
    }

    public String getCheckInDate() {
        sharedPreferences = _context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
        return sharedPreferences.getString("check_in_date", null);
/*
        Log.v("LEADIDDDDD", "",+sharedPreferences)
*/
    }

}
