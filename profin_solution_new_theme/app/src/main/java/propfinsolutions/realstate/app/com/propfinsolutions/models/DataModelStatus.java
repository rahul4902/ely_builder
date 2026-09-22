package propfinsolutions.realstate.app.com.propfinsolutions.models;

public class DataModelStatus {
    public String text;
    public int count;
    public int drawable;
    public String color;
    public DataModelStatus(String t, int count_number, int d, String c ) {
        text=t;
        count=count_number;
        drawable=d;
        color=c;
    }
}
