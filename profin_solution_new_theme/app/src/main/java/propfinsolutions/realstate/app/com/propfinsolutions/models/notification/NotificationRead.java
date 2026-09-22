package propfinsolutions.realstate.app.com.propfinsolutions.models.notification;
import com.google.gson.annotations.SerializedName;
import java.util.List;
public class NotificationRead {
    @SerializedName("read")
    List<Read> read;

    @SerializedName("message")
    List<NotificationRead.Message> message;


    public void setRead(List<Read> read) {
        this.read = read;
    }
    public List<Read> getRead() {
        return read;
    }

    public void setMessage(List<NotificationRead.Message> message) {
        this.message = message;
    }
    public List<NotificationRead.Message> getMessage() {
        return message;
    }

    public class Read {

        @SerializedName("msg")
        String msg;

        @SerializedName("status")
        int status;


        public void setMsg(String msg) {
            this.msg = msg;
        }
        public String getMsg() {
            return msg;
        }

        public void setStatus(int status) {
            this.status = status;
        }
        public int getStatus() {
            return status;
        }

    }

    public class Message {

        @SerializedName("msg")
        String msg;

        @SerializedName("status")
        int status;


        public void setMsg(String msg) {
            this.msg = msg;
        }
        public String getMsg() {
            return msg;
        }

        public void setStatus(int status) {
            this.status = status;
        }
        public int getStatus() {
            return status;
        }

    }
}
