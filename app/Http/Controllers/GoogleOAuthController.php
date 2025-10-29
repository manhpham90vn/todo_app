<?php

namespace App\Http\Controllers;

use App\Models\GoogleToken;
use App\Services\GoogleCalendarClient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Throwable;

class GoogleOAuthController extends Controller
{
    public function __construct() {}

    public function redirect(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        $client = GoogleCalendarClient::makeClient();
        $client->setState($state);

        return redirect()->away($client->createAuthUrl());
    }

    public function callback(Request $request)
    {
        try {
            $code = $request->string('code');
            $state = $request->string('state');

            if ($code->isEmpty()) {
                abort(400, 'Missing code');
            }

            $expected = $request->session()->pull('google_oauth_state');
            if (! $expected || $expected !== $state) {
                abort(400, 'Invalid OAuth state');
            }

            $client = GoogleCalendarClient::makeClient();
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                abort(400, $token['error_description'] ?? 'OAuth error');
            }

            GoogleToken::updateOrCreate(
                ['user_id' => auth()->id()],
                $token
            );

            return redirect()
                ->route('todos.index')
                ->with('success', 'Kết nối Google thành công. Token đã được lưu.');
        } catch (Throwable $e) {
            report($e);
            abort(400, 'OAuth callback failed');
        }
    }
}
