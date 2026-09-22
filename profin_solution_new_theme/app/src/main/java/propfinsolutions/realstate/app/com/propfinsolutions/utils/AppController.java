package propfinsolutions.realstate.app.com.propfinsolutions.utils;

import android.app.Application;
import android.content.Context;
import android.content.SharedPreferences;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.Signature;
import android.os.Build;
import android.util.Base64;
import android.util.Log;

import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;


public class AppController extends Application {
    public static final String TAG = AppController.class.getSimpleName();
    private SharedPreferences sharedPreferences;
    private Context mContext;
    private static AppController mInstance;

    @Override
    public void onCreate() {
        super.onCreate();
        mInstance = this;
        mContext = getApplicationContext();

        sharedPreferences = getSharedPreferences("app_prefs", Context.MODE_PRIVATE);


//        AppSignatureHelper appSignatureHelper = new AppSignatureHelper(mContext);
//        appSignatureHelper.getAppSignatures();

        try {
            PackageInfo info = getPackageManager().getPackageInfo(
                    getPackageName(),
                    PackageManager.GET_SIGNATURES);
            for (Signature signature : info.signatures) {
                MessageDigest md = MessageDigest.getInstance("SHA");
                md.update(signature.toByteArray());
                Log.e("YourKeyHash :", Base64.encodeToString(md.digest(), Base64.DEFAULT));
                System.out.println("YourKeyHash: " + Base64.encodeToString(md.digest(), Base64.DEFAULT));
            }
        } catch (PackageManager.NameNotFoundException | NoSuchAlgorithmException e) {
            e.printStackTrace();
        }
    }

    public static synchronized AppController getInstance() {
        return mInstance;
    }



//    @Override
//    protected void attachBaseContext(Context base) {
//        super.attachBaseContext(base);
//        MultiDex.install(this);
//    }



    public void isKeyBoolean(String key, boolean value) {
        final SharedPreferences SpyAppData = mContext.getSharedPreferences(getPackageName(), 0);
        SharedPreferences.Editor editor = SpyAppData.edit();
        editor.putBoolean(key, value);
        editor.commit();
    }

    public void saveKeyString(String key, String value) {
        SharedPreferences sp = mContext.getSharedPreferences(getPackageName(), 0);
        sp.edit().putString(key, value).commit();
    }

    public boolean getKeyBoolean(String key) {
        final SharedPreferences ToolsAppData = mContext.getSharedPreferences(getPackageName(), 0);
        return ToolsAppData.getBoolean(key, false);
    }

    public String getKeyString(String key) {
        SharedPreferences sp = mContext.getSharedPreferences(getPackageName(), 0);
        return sp.getString(key, null);
    }


    public int getVolume() {
        // Assuming volume is stored in shared preferences
        return sharedPreferences.getInt("volume", 5); // Default value is 5
    }

    public void setVolume(int volume) {
        // To set the volume value in shared preferences
        SharedPreferences.Editor editor = sharedPreferences.edit();
        editor.putInt("volume", volume);
        editor.apply();
    }



}