<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;
use PDO;

final class DriveFolderRepository {
    public function __construct(private readonly PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO drive_folders (channel_id, user_id, parent_folder_id, google_drive_folder_id, name, folder_type)
             VALUES (:channel_id, :user_id, :parent_id, :drive_id, :name, :type)'
        );
        $stmt->execute([
            'channel_id' => $data['channel_id'],
            'user_id' => $data['user_id'],
            'parent_id' => $data['parent_folder_id'] ?? null,
            'drive_id' => $data['google_drive_folder_id'],
            'name' => $data['name'],
            'type' => $data['folder_type'],
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function allForChannel(int $channelId): array {
        $stmt = $this->db->prepare('SELECT * FROM drive_folders WHERE channel_id = :c');
        $stmt->execute(['c' => $channelId]);
        return $stmt->fetchAll();
    }
}