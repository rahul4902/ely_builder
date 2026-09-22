package propfinsolutions.realstate.app.com.propfinsolutions.models.calllog;
import java.util.List;

import com.google.gson.annotations.SerializedName;
public class AddCallLogs {
    @SerializedName("call_logs")
    List<CallLogs> callLogs;

    public void setCallLogs(List<CallLogs> callLogs) {
        this.callLogs = callLogs;
    }
    public List<CallLogs> getCallLogs() {
        return callLogs;
    }

    public class CallLogs {

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
