<?php
declare(strict_types=1);
namespace App\Services\Google;

use App\Core\Env;
use Google\Client;
use Google\Service\Oauth2;

final class GoogleOAuthService {
    public const SCOPES_STEP1 = [
        'openid', 'email', 'profile',
        'https://www.googleapis.com/auth/drive.file',
    ];
    public const SCOPES_STEP2 = [
        'https://www.googleapis.com/auth/youtube',
    ];

    public static function client(array $scopes = []): Client {
        $client = new Client();
        $client->setClientId((string) Env::get('GOOGLE_CLIENT_ID'));
        $client->setClientSecret((string) Env::get('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri((string) Env::get('GOOGLE_REDIRECT_URI'));
        if ($scopes) $client->setScopes($scopes);
        $client->setAccessType('offline');
        $client->setPrompt('consent');
<<<<<<< HEAD
        // $client->setIncludeGrantedScopes(true);
=======
        $client->setIncludeGrantedScopes(true);
>>>>>>> 067a99341dd5b6ec8afa469d8b52f9ad0d07508c
        return $client;
    }

    public function getAuthUrl(string $step = 'step1'): string {
        $scopes = $step === 'step2' ? self::SCOPES_STEP2 : self::SCOPES_STEP1;
        $client = self::client($scopes);
        $client->setState($step);
        return $client->createAuthUrl();
    }

    /** @return array{tokens: array, profile: ?array} */
    public function handleCallback(string $code, string $step): array {
        $scopes = $step === 'step2' ? self::SCOPES_STEP2 : self::SCOPES_STEP1;
        $client = self::client($scopes);
        $tokens = $client->fetchAccessTokenWithAuthCode($code);
        if (isset($tokens['error'])) {
            throw new \RuntimeException('Google OAuth error: ' . $tokens['error']);
        }

        $profile = null;
        if ($step === 'step1') {
            $client->setAccessToken($tokens);
            $oauth2 = new Oauth2($client);
            $userInfo = $oauth2->userinfo->get();
            $profile = ['id' => $userInfo->getId(), 'email' => $userInfo->getEmail()];
        }

        return ['tokens' => $tokens, 'profile' => $profile];
    }
}