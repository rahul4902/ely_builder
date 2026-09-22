package propfinsolutions.realstate.app.com.propfinsolutions.utils;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.telephony.SmsMessage;
import android.util.Log;

import propfinsolutions.realstate.app.com.propfinsolutions.OtpReceivedInterface;
import propfinsolutions.realstate.app.com.propfinsolutions.ui.LoginActivity;

public class SmsBroadcastReceiver extends BroadcastReceiver {
    String messageBody;
    OtpReceivedInterface otpReceiveInterface = null;

    public void setOnOtpListeners(OtpReceivedInterface otpReceiveInterface) {
        this.otpReceiveInterface = otpReceiveInterface;
    }
    @Override
    public void onReceive(Context context, Intent intent) {
        try {
            Log.e("BROADCAST RECEIVER", "onReceive called()");
            Bundle data  = intent.getExtras();

            Object[] pdus = (Object[]) data.get("pdus");

            for(int i=0;i<pdus.length;i++){
                SmsMessage smsMessage = SmsMessage.createFromPdu((byte[]) pdus[i]);

                String sender = smsMessage.getDisplayOriginatingAddress();

                messageBody = smsMessage.getMessageBody();


                String otpMessage = messageBody.replace("Your verification code is ", "");

//        String otp = otpMessage.split("\n")[0];
                new SharedPrefClass(context).setOtp(otpMessage);

                Log.v("Sms-----","Sms"+otpMessage);
//          otpReceiveInterface.onOtpReceived(messageBody);


            }
        }catch (Exception ee){
            ee.printStackTrace();
        }


    }



}