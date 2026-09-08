<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;
use PDO;

final class GoogleAccountRepository {
    public function __construct(private readonly PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }

    public function findByUserAndGoogleId(int $userId, string $googleAccountId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM google_accounts WHERE user_id = :u AND google_account_id = :g LIMIT 1');
        $stmt->execute(['u' => $userId, 'g' => $googleAccountId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function find(int $id): ?array {
        $stmt = $this->db->prepare('SELECT * FROM google_accounts WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function allForUser(int $userId): array {
        $stmt = $this->db->prepare('SELECT * FROM google_accounts WHERE user_id = :u ORDER BY created_at DESC');
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO google_accounts
                (user_id, google_account_id, email, access_token_encrypted, refresh_token_encrypted, token_expiry, scopes, status, token_updated_at)
             VALUES
                (:user_id, :google_account_id, :email, :access, :refresh, :expiry, :scopes, :status, NOW())'
        );
        $stmt->execute([
            'user_id' => $data['user_id'],
            'google_account_id' => $data['google_account_id'],
            'email' => $data['email'],
            'access' => $data['access_token_encrypted'],
            'refresh' => $data['refresh_token_encrypted'],
            'expiry' => $data['token_expiry'],
            'scopes' => json_encode($data['scopes'] ?? []),
            'status' => $data['status'] ?? 'connected',
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function updateTokens(int $id, string $accessTokenEncrypted, ?string $refreshTokenEncrypted, string $tokenExpiry, array $scopes): void {
        $sql = 'UPDATE google_accounts SET access_token_encrypted = :access, token_expiry = :expiry, scopes = :scopes, token_updated_at = NOW(), status = "connected"';
        $params = ['access' => $accessTokenEncrypted, 'expiry' => $tokenExpiry, 'scopes' => json_encode($scopes), 'id' => $id];
        if ($refreshTokenEncrypted !== null) {
            $sql .= ', refresh_token_encrypted = :refresh';
            $params['refresh'] = $refreshTokenEncrypted;
        }
        $sql .= ' WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
    }

    public function updateStatus(int $id, string $status): void {
        $stmt = $this->db->prepare('UPDATE google_accounts SET status = :status WHERE id = :id');
        $stmt->execute(['status' => $status, 'id' => $id]);
    }
}