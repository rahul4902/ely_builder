package propfinsolutions.realstate.app.com.propfinsolutions.models.notification;
import java.util.List;
import com.google.gson.annotations.SerializedName;
public class NotificationResponseModel {
    @SerializedName("UserNotifications")
    List<UserNotifications> UserNotifications;

    @SerializedName("message")
    List<Message> message;

    public void setUserNotifications(List<UserNotifications> UserNotifications) {
        this.UserNotifications = UserNotifications;
    }
    public List<UserNotifications> getUserNotifications() {
        return UserNotifications;
    }

    public void setMessage(List<Message> message) {
        this.message = message;
    }
    public List<Message> getMessage() {
        return message;
    }

    public class UserNotifications {

        @SerializedName("id")
        String id;

        @SerializedName("user_id")
        String userId;

        @SerializedName("title")
        String title;

        @SerializedName("content")
        String content;

        @SerializedName("n_read")
        String nRead;

        @SerializedName("created_at")
        String createdAt;


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

        public void setTitle(String title) {
            this.title = title;
        }
        public String getTitle() {
            return title;
        }

        public void setContent(String content) {
            this.content = content;
        }
        public String getContent() {
            return content;
        }

        public void setNRead(String nRead) {
            this.nRead = nRead;
        }
        public String getNRead() {
            return nRead;
        }

        public void setCreatedAt(String createdAt) {
            this.createdAt = createdAt;
        }
        public String getCreatedAt() {
            return createdAt;
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
