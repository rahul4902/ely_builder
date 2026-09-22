package propfinsolutions.realstate.app.com.propfinsolutions.utils;

import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;

/**
 * Created by Divakar on 7/14/2017.
 */

public class Formatter {

    public String formatDate(String mytime)
    {

        // String mytime="Jan 17, 2012";
       // String mytime="15 May 2017";
        SimpleDateFormat dateFormat = new SimpleDateFormat(
                "dd MMM yyyy");
        Date myDate = null;
        try {
            myDate = dateFormat.parse(mytime);

        } catch (ParseException e) {
            e.printStackTrace();
        }

        SimpleDateFormat timeFormat = new SimpleDateFormat("yyyy-MM-dd");
        String finalDate = timeFormat.format(myDate);

        System.out.println(finalDate);
        return finalDate;
    }
}
