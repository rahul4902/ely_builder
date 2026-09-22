package propfinsolutions.realstate.app.com.propfinsolutions.utils;

import android.app.ProgressDialog;
import android.content.Context;

/**
 * Created by Divakar on 7/7/2017.
 */

public class DialogClass {

    private static DialogClass dialogClass = null;
    /*
    * variable initialize
     */
    Context _context;
    ProgressDialog progressDoalog;


    private DialogClass()
    {    }

    public static DialogClass getInstance()
    {
        if( dialogClass == null)
        {
            dialogClass = new DialogClass();
        }
        return dialogClass;
    }

    public void startProgress(Context _context)
    {
        progressDoalog = new ProgressDialog(_context);
        progressDoalog.setMessage("Working! Please wait...");
       progressDoalog.setIndeterminate(true);
        progressDoalog.setProgressStyle(ProgressDialog.STYLE_SPINNER);
        // show it
       progressDoalog.show();
    }
    public void stopProgress()
    {
        try {
            progressDoalog.dismiss();
        }catch (NullPointerException npe)
        {

        }
    }

}
