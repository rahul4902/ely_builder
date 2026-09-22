<?php

namespace App\Services;

use GuzzleHttp\Client;

class FCMService
{
    public static function sendMessage($title, $body, $token, $data = [])
    {
        $serviceAccount = self::serviceAccount();
        $data = array_merge([
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'id' => '1',
            'status' => 'done',
        ], $data);

        $projectId = config('services.firebase.project_id') ?: ($serviceAccount['project_id'] ?? null);
        if (!$projectId) {
            throw new \RuntimeException('Firebase project ID is unavailable.');
        }

        $client = new Client();
        $response = $client->post("https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send", [
            'headers' => [
                'Authorization' => 'Bearer ' . self::getAccessToken($serviceAccount),
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'message' => [
                    'token' => $token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data' => $data,
                    'android' => [
                        'notification' => [
                            'channelId' => '1002',
                            'defaultSound' => false,
                            'notificationCount' => 1,
                            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                            'sound' => 'notification_sound.mp3',
                            'color' => '#000000',
                        ],
                    ],
                ],
            ],
        ]);

        return $response->getBody()->getContents();
    }

    private static function getAccessToken(array $serviceAccount)
    {
        $client = new Client();
        $response = $client->post('https://www.googleapis.com/oauth2/v4/token', [
            'form_params' => [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => self::generateJwt($serviceAccount),
            ],
        ]);

        $data = json_decode($response->getBody(), true);
        if (empty($data['access_token'])) {
            throw new \RuntimeException('Firebase access token could not be created.');
        }

        return $data['access_token'];
    }

    private static function generateJwt(array $serviceAccount)
    {
        $now = time();

        return \Firebase\JWT\JWT::encode([
            'iss' => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ], $serviceAccount['private_key'], 'RS256');
    }

    private static function serviceAccount()
    {
        $path = config('services.firebase.credentials_path');
        if (!$path || !is_readable($path)) {
            throw new \RuntimeException('Firebase service-account credentials are unavailable.');
        }

        $serviceAccount = json_decode(file_get_contents($path), true);
        if (!is_array($serviceAccount) || empty($serviceAccount['client_email']) || empty($serviceAccount['private_key'])) {
            throw new \RuntimeException('Firebase service-account credentials are invalid.');
        }

        return $serviceAccount;
    }
}
