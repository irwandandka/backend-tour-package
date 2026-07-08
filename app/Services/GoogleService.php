<?php

namespace App\Services;

use App\Models\User;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;

class GoogleService
{
    private $client;

    public function __construct()
    {
        $this->client = new GoogleClient;
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setRedirectUri(config('services.google.redirect_uri'));
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.email');
        $this->client->addScope('https://www.googleapis.com/auth/userinfo.profile');
    }

    public function getAuthUrl(string $redirectUri): string
    {
        $this->client->setRedirectUri($redirectUri);

        return $this->client->createAuthUrl();
    }

    public function getUserData(string $code): array
    {
        // Exchange authorization code for access token
        $accessToken = $this->client->fetchAccessTokenWithAuthCode($code);

        if (isset($accessToken['error'])) {
            throw new \Exception('Error fetching access token: '.$accessToken['error_description']);
        }

        $this->client->setAccessToken($accessToken);

        // Retrieve user info
        $oauth2 = new Oauth2($this->client);
        $googleUser = $oauth2->userinfo->get();

        return [
            'id' => $googleUser->id,
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'avatar' => $googleUser->picture,
        ];
    }
}
