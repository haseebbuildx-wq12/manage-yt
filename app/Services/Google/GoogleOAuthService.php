<?php
declare(strict_types=1);
namespace App\Services\Google;

use App\Core\Env;
use Google\Client;
use Google\Service\Oauth2;

final class GoogleOAuthService {
    public const SCOPES = [
        'openid',
        'email',
        'profile',
        'https://www.googleapis.com/auth/youtube',
        'https://www.googleapis.com/auth/yt-analytics.readonly',
        'https://www.googleapis.com/auth/drive.file',
    ];

    public static function client(): Client {
        $client = new Client();
        $client->setClientId((string) Env::get('GOOGLE_CLIENT_ID'));
        $client->setClientSecret((string) Env::get('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri((string) Env::get('GOOGLE_REDIRECT_URI'));
        $client->setScopes(self::SCOPES);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
        return $client;
    }

    public function getAuthUrl(): string {
        return self::client()->createAuthUrl();
    }

    /** @return array{tokens: array, profile: array} */
    public function handleCallback(string $code): array {
        $client = self::client();
        $tokens = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($tokens['error'])) {
            throw new \RuntimeException('Google OAuth error: ' . $tokens['error']);
        }
        $client->setAccessToken($tokens);
        $oauth2 = new Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        return [
            'tokens' => $tokens,
            'profile' => ['id' => $userInfo->getId(), 'email' => $userInfo->getEmail()],
        ];
    }
}