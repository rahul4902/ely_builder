<?php

return [
    'driver' => env('FCM_PROTOCOL', 'http'),
    'log_enabled' => true,

    'http' => [
        //'server_key' => env('FCM_SERVER_KEY', 'AAAAyL0t8K4:APA91bG_L_JoFbewiXMCEkmbLgtNF01hVGF4y13K7G1UAkqZxJ68LTqccH-VFpeQp_IHGJZ2zrKylY1sk0tXh3ALWdubxelfscgdvP7N3JCIjXu8cLonIs--QxHrJRJ4Z24vMDlfe7vW'),
        //'sender_id' => env('FCM_SENDER_ID', '862167363758'),
        //'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        //'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        //'timeout' => 30.0, // in second
		
		'server_key' => env('FCM_SERVER_KEY', 'AAAAkKWaSJ4:APA91bHX7l9RhU7429fhmejPXWptCDAJjwwRCxkhv5cwTN_MKX5u3G8zi1v52Ip8qAZELCP7XduZNsSSH8ayvJlpb9ENfA1OuM1fsddntLXQiDrr8L8Z0lrNHxTU2c-rmoDjoa53GruI'),
        'sender_id' => env('FCM_SENDER_ID', '621253642398'),
        'server_send_url' => 'https://fcm.googleapis.com/fcm/send',
        'server_group_url' => 'https://android.googleapis.com/gcm/notification',
        'timeout' => 30.0, // in second
		
		
		
		
    ],
];
