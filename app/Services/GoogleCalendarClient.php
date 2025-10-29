<?php

namespace App\Services;

use App\Models\GoogleToken;
use Google\Client;
use Google\Service\Calendar;

class GoogleCalendarClient
{
    public static function make(GoogleToken $token): Calendar
    {
        $client = new Client;
        $client->setApplicationName(config('app.name'));
        $client->setScopes([
            Calendar::CALENDAR_READONLY,
            'openid', 'email',
        ]);
        $client->setAuthConfig([
            'client_id' => config('google.google_calendar_client_id'),
            'client_secret' => config('google.google_calendar_secret'),
            'redirect_uris' => [config('google.google_calendar_redirect_uri')],
            'auth_uri' => 'https://accounts.google.com/o/oauth2/auth',
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ]);
        $client->setRedirectUri(config('google.google_calendar_redirect_uri'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        $client->setIncludeGrantedScopes(true);
        $client->setAccessToken($token->toArray());

        if ($client->isAccessTokenExpired()) {
            if (! $client->getRefreshToken()) {
                throw new \RuntimeException('Google Calendar token expired and no refresh token is available. Re-run the OAuth login flow.');
            }

            $refreshed = $client->fetchAccessTokenWithRefreshToken($client->getRefreshToken());
            if (isset($refreshed['error'])) {
                throw new \RuntimeException(
                    sprintf('Failed to refresh Google Calendar token (%s). Please re-run the OAuth login flow.', $refreshed['error'])
                );
            }

            $client->setAccessToken($refreshed);
            $new_token = $client->getAccessToken();

            GoogleToken::create($new_token);
        }

        return new Calendar($client);
    }
}
