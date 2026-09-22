<?php

namespace App\Helpers;

use Google_Client;

class Firebase2

{
    public static function getAccessToken()
    {
        $credentialsPath = config('services.firebase.credentials_path');
        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        
        // Generate and return the OAuth 2.0 token
        return $client->fetchAccessTokenWithAssertion()['access_token'];
    }
}
