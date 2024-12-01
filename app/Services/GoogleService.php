<?php

namespace App\Services;

use Google_Client;
use Google_Service_Oauth2;
use App\Models\User;

class GoogleService
{
    protected $client;

    public function __construct()
    {
        $this->client = new Google\Client();
        $this->client->setClientId(env('GOOGLE_CLIENT_ID')); // Ambil dari .env
        $this->client->setClientSecret(env('GOOGLE_CLIENT_SECRET')); // Ambil dari .env
        $this->client->setRedirectUri(env('GOOGLE_REDIRECT_URI')); // Redirect URL Anda
        $this->client->addScope('email');
        $this->client->addScope('profile');
    }

    /**
     * Mendapatkan URL untuk redirect ke Google
     */
    public function getGoogleAuthUrl()
    {
        return $this->client->createAuthUrl();
    }

    /**
     * Mengambil data user dari Google menggunakan kode authorization
     */
    public function getUserInfo($code)
    {
        // Ambil access token
        $this->client->fetchAccessTokenWithAuthCode($code);

        // Buat instance Google_Service_Oauth2
        $oauthService = new Google\Service\Oauth2($this->client);

        // Ambil informasi user
        $userInfo = $oauthService->userinfo->get();

        return $userInfo;
    }
}
