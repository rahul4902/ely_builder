package propfinsolutions.realstate.app.com.propfinsolutions.models.checkInOut;
import com.google.gson.annotations.SerializedName;

import java.util.List;


public class CheckInOutModel {
    @SerializedName("status")
    String status;
    @SerializedName("data")
    Data data;
    public void setStatus(String status) {
        this.status = status;
    }
    public String getStatus() {
        return status;
    }

    public void setData(Data data) {
        this.data = data;
    }
    public Data getData() {
        return data;
    }

    public class Data {

        @SerializedName("msg")
        String msg;

        @SerializedName("key")
        String key;

        @SerializedName("empcheckindata")
        List<Empcheckindata> empcheckindata;

        public void setMsg(String msg) {
            this.msg = msg;
        }
        public String getMsg() {
            return msg;
        }

        public void setKey(String key) {
            this.key = key;
        }
        public String getKey() {
            return key;
        }

        public void setEmpcheckindata(List<Empcheckindata> empcheckindata) {
            this.empcheckindata = empcheckindata;
        }
        public List<Empcheckindata> getEmpcheckindata() {
            return empcheckindata;
        }
    }
    public class Empcheckindata {

        @SerializedName("id")
        String id;

        @SerializedName("user_id")
        String userId;

        @SerializedName("check_in_date")
        String checkInDate;

        @SerializedName("check_in_lat")
        String checkInLat;

        @SerializedName("check_in_lng")
        String checkInLng;

        @SerializedName("check_in_rem")
        String checkInRem;

        @SerializedName("check_in_address")
        String checkInAddress;

        @SerializedName("check_out_lat")
        String checkOutLat;

        @SerializedName("check_out_lng")
        String checkOutLng;

        @SerializedName("check_out_address")
        String checkOutAddress;

        @SerializedName("check_out_rem")
        String checkOutRem;

        @SerializedName("check_out_date")
        String checkOutDate;


        public void setId(String id) {
            this.id = id;
        }
        public String getId() {
            return id;
        }

        public void setUserId(String userId) {
            this.userId = userId;
        }
        public String getUserId() {
            return userId;
        }

        public void setCheckInDate(String checkInDate) {
            this.checkInDate = checkInDate;
        }
        public String getCheckInDate() {
            return checkInDate;
        }

        public void setCheckInLat(String checkInLat) {
            this.checkInLat = checkInLat;
        }
        public String getCheckInLat() {
            return checkInLat;
        }

        public void setCheckInLng(String checkInLng) {
            this.checkInLng = checkInLng;
        }
        public String getCheckInLng() {
            return checkInLng;
        }

        public void setCheckInRem(String checkInRem) {
            this.checkInRem = checkInRem;
        }
        public String getCheckInRem() {
            return checkInRem;
        }

        public void setCheckInAddress(String checkInAddress) {
            this.checkInAddress = checkInAddress;
        }
        public String getCheckInAddress() {
            return checkInAddress;
        }

        public void setCheckOutLat(String checkOutLat) {
            this.checkOutLat = checkOutLat;
        }
        public String getCheckOutLat() {
            return checkOutLat;
        }

        public void setCheckOutLng(String checkOutLng) {
            this.checkOutLng = checkOutLng;
        }
        public String getCheckOutLng() {
            return checkOutLng;
        }

        public void setCheckOutAddress(String checkOutAddress) {
            this.checkOutAddress = checkOutAddress;
        }
        public String getCheckOutAddress() {
            return checkOutAddress;
        }

        public void setCheckOutRem(String checkOutRem) {
            this.checkOutRem = checkOutRem;
        }
        public String getCheckOutRem() {
            return checkOutRem;
        }

        public void setCheckOutDate(String checkOutDate) {
            this.checkOutDate = checkOutDate;
        }
        public String getCheckOutDate() {
            return checkOutDate;
        }

    }
}
