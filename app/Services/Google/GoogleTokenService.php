<?php
declare(strict_types=1);
namespace App\Services\Google;

use App\Core\Encryption;
use App\Repositories\GoogleAccountRepository;
use Google\Client;

final class GoogleTokenService {
    public function __construct(private readonly GoogleAccountRepository $repository) {}
    public static function make(): self { return new self(GoogleAccountRepository::make()); }

    public function isAccessTokenExpired(array $account): bool {
        if (empty($account['token_expiry'])) return true;
        return strtotime($account['token_expiry']) <= time() + 60;
    }

    public function getValidAccessToken(array $account): string {
        if (!$this->isAccessTokenExpired($account)) {
            return Encryption::decrypt($account['access_token_encrypted']);
        }
        return $this->refreshAccessToken($account);
    }

    public function refreshAccessToken(array $account): string {
        if (empty($account['refresh_token_encrypted'])) {
            $this->invalidateConnection((int) $account['id']);
            throw new \RuntimeException('No refresh token available. Reconnect required.');
        }

        $client = GoogleOAuthService::client();
        $refreshToken = Encryption::decrypt($account['refresh_token_encrypted']);

        try {
            $tokens = $client->fetchAccessTokenWithRefreshToken($refreshToken);
        } catch (\Throwable $e) {
            $this->invalidateConnection((int) $account['id']);
            throw new \RuntimeException('Google refresh token invalid or revoked.', 0, $e);
        }

        if (isset($tokens['error'])) {
            $this->invalidateConnection((int) $account['id']);
            throw new \RuntimeException('Google refresh token invalid or revoked.');
        }

        $this->saveRefreshedToken((int) $account['id'], $tokens);
        return $tokens['access_token'];
    }

    public function saveRefreshedToken(int $accountId, array $tokens): void {
        $expiry = date('Y-m-d H:i:s', time() + (int) ($tokens['expires_in'] ?? 3600));
        $this->repository->updateTokens(
            $accountId,
            Encryption::encrypt($tokens['access_token']),
            isset($tokens['refresh_token']) ? Encryption::encrypt($tokens['refresh_token']) : null,
            $expiry,
            explode(' ', $tokens['scope'] ?? '')
        );
    }

    public function invalidateConnection(int $accountId): void {
        $this->repository->updateStatus($accountId, 'needs_reconnect');
    }

    public function clientFor(array $account): Client {
        $accessToken = $this->getValidAccessToken($account);
        $client = GoogleOAuthService::client();
        $client->setAccessToken(['access_token' => $accessToken]);
        return $client;
    }
}