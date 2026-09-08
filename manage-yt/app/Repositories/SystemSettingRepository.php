<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;
use PDO;

final class SystemSettingRepository {
    public function __construct(private readonly PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }

    /**
     * Fetch every global (user_id IS NULL) setting as [key => value].
     */
    public function all(): array {
        $stmt = $this->db->query('SELECT setting_key, setting_value FROM system_settings WHERE user_id IS NULL');
        $rows = $stmt ? $stmt->fetchAll() : [];
        $out = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    public function get(string $key, ?string $default = null): ?string {
        $stmt = $this->db->prepare('SELECT setting_value FROM system_settings WHERE user_id IS NULL AND setting_key = :key LIMIT 1');
        $stmt->execute(['key' => $key]);
        $value = $stmt->fetchColumn();
        return $value === false ? $default : (string) $value;
    }

    /**
     * Insert or update a single global setting.
     */
    public function set(string $key, ?string $value, bool $isSecret = false): void {
        $stmt = $this->db->prepare(
            'INSERT INTO system_settings (user_id, setting_key, setting_value, is_secret)
             VALUES (NULL, :key, :value, :is_secret)
             ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), is_secret = VALUES(is_secret)'
        );
        $stmt->execute([
            'key' => $key,
            'value' => $value,
            'is_secret' => $isSecret ? 1 : 0,
        ]);
    }

    /**
     * Persist multiple settings in one transaction.
     * @param array<string,string|null> $pairs
     */
    public function setMany(array $pairs, bool $isSecret = false): void {
        $this->db->beginTransaction();
        try {
            foreach ($pairs as $key => $value) {
                $this->set($key, $value, $isSecret);
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
