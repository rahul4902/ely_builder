package propfinsolutions.realstate.app.com.propfinsolutions.models.viewdetail;

import com.google.gson.annotations.SerializedName;

import java.util.List;

public class Seniors {

    @SerializedName("Seniors")
    public List<Seniors_lsit> data;
    public  class Seniors_lsit{
        @SerializedName("name")
        public String name;

    }
}
