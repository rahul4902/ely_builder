package propfinsolutions.realstate.app.com.propfinsolutions.push_notification;

import android.annotation.SuppressLint;
import android.app.Notification;
import android.app.NotificationChannel;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;

import android.media.AudioAttributes;
import android.media.AudioManager;
import android.media.RingtoneManager;
import android.net.Uri;
import android.os.Build;

import android.util.Log;
import androidx.core.app.NotificationCompat;
import com.google.firebase.messaging.FirebaseMessagingService;
import com.google.firebase.messaging.RemoteMessage;


import propfinsolutions.realstate.app.com.propfinsolutions.R;

import propfinsolutions.realstate.app.com.propfinsolutions.ui.ViewDetailActivityOne;
import propfinsolutions.realstate.app.com.propfinsolutions.utils.AppController;


public class MyFirebaseMessagingService extends FirebaseMessagingService {
    private static final String TAG = "MyFirebaseMessagingServ";
    Context context;
    String title,body;
    @Override
    public void onMessageReceived(RemoteMessage remoteMessage) {
        super.onMessageReceived(remoteMessage);
        if(remoteMessage.getData()!=null);
        String data = String.valueOf(remoteMessage.getData());
        Log.v("DATA","DATA RESPONSE - " + data);
       /* title = remoteMessage.getNotification().getTitle();
        body = remoteMessage.getNotification().getBody();  */
        title = remoteMessage.getNotification().getTitle();
        body = remoteMessage.getNotification().getBody();
        String[] separated = body.split("\\.");
//        Log.v("title 0 : ","title - "+separated[0]);
//        Log.v("title 1 : ","title - "+separated[1]);
    //   new SharedPrefClass(context).setLead_ID(separated[1]);
       // generateNotification(body,title);
//        SharedPreferences prefs = this.getSharedPreferences("leadid", Context.MODE_PRIVATE);
//        prefs.edit().putInt("idlead", Integer.parseInt(separated[1])).apply();
        sendNotification();

        /*  int type=getSharedPreferences("login_info",MODE_PRIVATE).getInt("usertype",-1);
        Map<String, String> data = remoteMessage.getData();
        String body = data.get("body");
        String title = data.get("title");

        Intent intent;
        if(type==2){
            intent = new Intent(getApplicationContext(), HomeActivity.class);
        }
        else
            intent = new Intent(getApplicationContext(), ViewDetailActivityOne.class);

        PendingIntent pi = PendingIntent.getActivity(getApplicationContext(), 101, intent, 0);

        NotificationManager nm = (NotificationManager) getApplicationContext().getSystemService(NOTIFICATION_SERVICE);

        NotificationChannel channel = null;
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.O) {

            AudioAttributes att = new AudioAttributes.Builder()
                    .setUsage(AudioAttributes.USAGE_NOTIFICATION)
                    .setContentType(AudioAttributes.CONTENT_TYPE_SPEECH)
                    .build();

            channel = new NotificationChannel("222", "my_channel", NotificationManager.IMPORTANCE_HIGH);
            nm.createNotificationChannel(channel);
        }
                NotificationCompat.Builder builder =
                new NotificationCompat.Builder(
                        getApplicationContext(), "222")
                        .setContentTitle(title)
                        .setAutoCancel(true)
                        .setLargeIcon(((BitmapDrawable)getDrawable(R.drawable.logo)).getBitmap())
                        .setSmallIcon(R.drawable.logo)
                        //.setSound(Uri.parse("android.resource://" + getPackageName() + "/" + R.raw.electro))
                        .setContentText(body)
                        .setSmallIcon(R.drawable.logo)
                        .setContentIntent(pi)
                ;

        builder.setPriority(NotificationCompat.PRIORITY_HIGH);
        nm.notify(101, builder.build())*/;

       }

    public void sendNotification() {
        NotificationCompat.BigPictureStyle style = new NotificationCompat.BigPictureStyle();
        Uri soundUri = Uri.parse("android.resource://" + getPackageName() + "/" + R.raw.notification_sound);

        AudioManager audioManager = (AudioManager) getSystemService(Context.AUDIO_SERVICE);
        int desiredVolume = AppController.getInstance().getVolume();
        int maxVolume = audioManager.getStreamMaxVolume(AudioManager.STREAM_NOTIFICATION);
        int minVolume = 0;
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.P) {
            minVolume = audioManager.getStreamMinVolume(AudioManager.STREAM_NOTIFICATION);
        }

        if (desiredVolume < minVolume) {
            desiredVolume = minVolume;
        } else if (desiredVolume > maxVolume || desiredVolume == 100) {
            desiredVolume = maxVolume;
        }

        audioManager.setStreamVolume(AudioManager.STREAM_NOTIFICATION, desiredVolume, 0);

        Intent intent = new Intent(getApplicationContext(), ViewDetailActivityOne.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
        PendingIntent pendingIntent = PendingIntent.getActivity(getApplicationContext(), 0, intent, PendingIntent.FLAG_ONE_SHOT | PendingIntent.FLAG_IMMUTABLE);

        NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

        String NOTIFICATION_CHANNEL_ID = "101";

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
            NotificationChannel existingChannel = notificationManager.getNotificationChannel(NOTIFICATION_CHANNEL_ID);
//            if (existingChannel != null) {
//                Log.d("NotificationLog", "Existing channel found, deleting: " + existingChannel.getId());
//                notificationManager.deleteNotificationChannel(NOTIFICATION_CHANNEL_ID);  // Delete the existing channel
//            } else {
//                Log.d("NotificationLog", "No existing channel found.");
//            }
            if (existingChannel == null) {
                Log.d("NotificationChannel", "Channel not found, creating a new one.");
                // Create channel code...
            } else {
                Log.d("NotificationChannel", "Channel already exists: " + existingChannel.getId());
            }

            // Create a new channel with custom sound
            NotificationChannel notificationChannel = new NotificationChannel(NOTIFICATION_CHANNEL_ID, "Notification", NotificationManager.IMPORTANCE_HIGH);

            AudioAttributes audioAttributes = new AudioAttributes.Builder()
                    .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
                    .setUsage(AudioAttributes.USAGE_NOTIFICATION)
                    .build();

            notificationChannel.setDescription("Notifications");
            notificationChannel.enableLights(true);
            notificationChannel.setVibrationPattern(new long[]{1000, 100, 1000, 100});
            notificationChannel.enableVibration(true);
            notificationChannel.setSound(soundUri, audioAttributes);  // Set the custom sound

            Log.d("NotificationLog", "Creating new channel with ID: " + NOTIFICATION_CHANNEL_ID);
            Log.d("NotificationLog", "Channel Sound URI: " + soundUri.toString());
            notificationManager.createNotificationChannel(notificationChannel);
        }

        NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this, NOTIFICATION_CHANNEL_ID)
                .setSmallIcon(R.mipmap.ic_launcher_round)
                .setContentTitle(title)
                .setSound(soundUri, AudioManager.STREAM_NOTIFICATION)  // Set sound here for backward compatibility
                .setContentText(body)
                .setContentIntent(pendingIntent)
                .setStyle(style)
                .setWhen(System.currentTimeMillis())
                .setPriority( NotificationManager.IMPORTANCE_HIGH);

        Log.d("NotificationLog", "Notification Builder created with sound URI: " + soundUri.toString());

        notificationManager.notify(2, notificationBuilder.build());
        Log.d("NotificationLog", "Notification sent with ID: 101");
        Log.d("NotificationLog", "Notification channel with ID: " + NOTIFICATION_CHANNEL_ID + " is being created");

    }


//        public void sendNotification(){
//        NotificationCompat.BigPictureStyle style = new NotificationCompat.BigPictureStyle();
//        //style.bigPicture(bitmap);
//       // Uri defaultSound = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
//     //   Uri soundUri = Uri.parse("android.resource://" +getPackageName() +"/"+ R.raw.notification_sound);
//            Uri soundUri = Uri.parse("android.resource://" + getPackageName() + "/" + R.raw.notification_sound);
////        AudioManager audioManager = (AudioManager) getSystemService(Context.AUDIO_SERVICE);
//
//            //
//            AudioManager audioManager = (AudioManager) getSystemService(Context.AUDIO_SERVICE);
//            int desiredVolume = AppController.getInstance().getVolume();
//            ;//sharedPreferences.getInt("volume", 0);// Obtain the desired volume from the user
//            int maxVolume = audioManager.getStreamMaxVolume(AudioManager.STREAM_NOTIFICATION);
//            int minVolume = 0;
//            if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.P) {
//                minVolume = audioManager.getStreamMinVolume(AudioManager.STREAM_NOTIFICATION);
//            }
//
//// Ensure the desired volume is within the valid range
//            if (desiredVolume < minVolume) {
//                desiredVolume = minVolume;
//            } else if (desiredVolume > maxVolume || desiredVolume == 100) {
//                desiredVolume = maxVolume;
//            }
//
//            audioManager.setStreamVolume(AudioManager.STREAM_NOTIFICATION, desiredVolume, 0);
//            //
//
//        Intent intent = new Intent(getApplicationContext(), ViewDetailActivityOne.class);
//       // intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_SINGLE_TOP);
//        intent.setFlags(Intent. FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
//
//        PendingIntent pendingIntent = PendingIntent.getActivity(getApplicationContext(), 0, intent, PendingIntent.FLAG_ONE_SHOT | PendingIntent.FLAG_IMMUTABLE);
//
//        NotificationManager notificationManager = (NotificationManager)getSystemService(Context.NOTIFICATION_SERVICE);
//
//        String NOTIFICATION_CHANNEL_ID = "101";
//        if(Build.VERSION.SDK_INT >= Build.VERSION_CODES.O){
//            @SuppressLint("WrongConstant")
//            NotificationChannel notificationChannel = new NotificationChannel(NOTIFICATION_CHANNEL_ID, "Notification", NotificationManager.IMPORTANCE_HIGH);
//
//            AudioAttributes audioAttributes = new AudioAttributes.Builder()
//                    .setContentType(AudioAttributes. CONTENT_TYPE_SPEECH )
//                    .setUsage(AudioAttributes. USAGE_NOTIFICATION )
//                    .build() ;
//
//            //Configure Notification Channel
//            notificationChannel.setDescription("Notifications");
//            notificationChannel.enableLights(true);
//            notificationChannel.setVibrationPattern(new long[]{1000, 100, 1000, 100});
//            notificationChannel.enableVibration(true);
//            notificationChannel.setSound(soundUri , audioAttributes) ;
//            notificationManager.createNotificationChannel(notificationChannel);
//        }
//
//             NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this, NOTIFICATION_CHANNEL_ID)
//                .setSmallIcon(R.mipmap.ic_launcher_round)
//                .setContentTitle(title)
//                .setSound(soundUri, AudioManager.STREAM_NOTIFICATION)
//                .setContentText(body)
//                .setContentIntent(pendingIntent)
//                .setStyle(style)
//                .setWhen(System.currentTimeMillis())
//                .setPriority( NotificationManager.IMPORTANCE_HIGH);
//
//        notificationManager.notify(2, notificationBuilder.build());
//         }

//    public void sendNotification() {
//        NotificationCompat.BigPictureStyle style = new NotificationCompat.BigPictureStyle();
//        Uri soundUri = Uri.parse("android.resource://" + getPackageName() + "/" + R.raw.notification_sound);
//
//        AudioManager audioManager = (AudioManager) getSystemService(Context.AUDIO_SERVICE);
//        int desiredVolume = AppController.getInstance().getVolume();
//        int maxVolume = audioManager.getStreamMaxVolume(AudioManager.STREAM_NOTIFICATION);
//        int minVolume = 0;
//        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.P) {
//            minVolume = audioManager.getStreamMinVolume(AudioManager.STREAM_NOTIFICATION);
//        }
//
//        if (desiredVolume < minVolume) {
//            desiredVolume = minVolume;
//        } else if (desiredVolume > maxVolume || desiredVolume == 100) {
//            desiredVolume = maxVolume;
//        }
//
//        audioManager.setStreamVolume(AudioManager.STREAM_NOTIFICATION, desiredVolume, 0);
//
//        Intent intent = new Intent(getApplicationContext(), ViewDetailActivityOne.class);
//        intent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP | Intent.FLAG_ACTIVITY_NEW_TASK);
//        PendingIntent pendingIntent = PendingIntent.getActivity(getApplicationContext(), 0, intent, PendingIntent.FLAG_ONE_SHOT | PendingIntent.FLAG_IMMUTABLE);
//
//        NotificationManager notificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
//
//        String NOTIFICATION_CHANNEL_ID = "101";
//        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.O) {
//            NotificationChannel existingChannel = notificationManager.getNotificationChannel(NOTIFICATION_CHANNEL_ID);
//            if (existingChannel != null) {
//                notificationManager.deleteNotificationChannel(NOTIFICATION_CHANNEL_ID);  // Delete the existing channel
//            }
//
//            // Create a new channel with custom sound
//            NotificationChannel notificationChannel = new NotificationChannel(NOTIFICATION_CHANNEL_ID, "Notification", NotificationManager.IMPORTANCE_HIGH);
//
//            AudioAttributes audioAttributes = new AudioAttributes.Builder()
//                    .setContentType(AudioAttributes.CONTENT_TYPE_SONIFICATION)
//                    .setUsage(AudioAttributes.USAGE_NOTIFICATION)
//                    .build();
//
//            notificationChannel.setDescription("Notifications");
//            notificationChannel.enableLights(true);
//            notificationChannel.setVibrationPattern(new long[]{1000, 100, 1000, 100});
//            notificationChannel.enableVibration(true);
//            notificationChannel.setSound(soundUri, audioAttributes);  // Set the custom sound
//            notificationManager.createNotificationChannel(notificationChannel);
//        }
//
//        NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this, NOTIFICATION_CHANNEL_ID)
//                .setSmallIcon(R.mipmap.ic_launcher_round)
//                .setContentTitle(title)
//                .setSound(soundUri, AudioManager.STREAM_NOTIFICATION)  // Set sound here for backward compatibility
//                .setContentText(body)
//                .setContentIntent(pendingIntent)
//                .setStyle(style)
//                .setWhen(System.currentTimeMillis())
//                .setPriority( NotificationManager.IMPORTANCE_HIGH);
//
//        notificationManager.notify(2, notificationBuilder.build());
//    }


/*
    private void generateNotification(String message, String title) {
         Intent intent = new Intent(getApplicationContext(), HomeActivity.class);
        intent.setFlags(Intent.FLAG_ACTIVITY_PREVIOUS_IS_TOP);
        NotificationManager mNotificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

        Uri defaultSoundUri = RingtoneManager.getDefaultUri(RingtoneManager.TYPE_NOTIFICATION);
        NotificationCompat.Builder notificationBuilder = new NotificationCompat.Builder(this)
                .setSmallIcon(R.mipmap.ic_launcher)  //a resource for your custom small icon
                .setContentTitle(title) //the "title" value you sent in your notification
                .setContentText(message) //ditto
                .setAutoCancel(true)  //dismisses the notification on click
                .setSound(defaultSoundUri);

        //Setting up Notification channels for android O and above
        if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.P) {
            int importance = NotificationManager.IMPORTANCE_HIGH;
            NotificationChannel notificationChannel = new NotificationChannel("3", "CHANNEL_NAME", importance);
            notificationChannel.enableLights(true);
            notificationChannel.setLightColor(Color.RED);
            notificationChannel.enableVibration(true);
            notificationChannel.setVibrationPattern(new long[]{100, 200, 300, 400, 500, 400, 300, 200, 400});
            assert mNotificationManager != null;
            notificationBuilder.setChannelId("3");
            mNotificationManager.createNotificationChannel(notificationChannel);
        }
        PendingIntent contentIntent = PendingIntent.getActivity(this, 3, intent, PendingIntent.FLAG_UPDATE_CURRENT);
        notificationBuilder.setContentIntent(contentIntent);
        mNotificationManager.notify(3, notificationBuilder.build());
    }*/

      }