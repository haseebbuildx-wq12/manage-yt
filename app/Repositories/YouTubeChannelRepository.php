<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;
use PDO;

final class YouTubeChannelRepository {
    public function __construct(private readonly PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }

    public function findByYoutubeChannelId(string $youtubeChannelId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM youtube_channels WHERE youtube_channel_id = :id LIMIT 1');
        $stmt->execute(['id' => $youtubeChannelId]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function allForUser(int $userId): array {
        $stmt = $this->db->prepare('SELECT * FROM youtube_channels WHERE user_id = :u ORDER BY created_at DESC');
        $stmt->execute(['u' => $userId]);
        return $stmt->fetchAll();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare(
            'INSERT INTO youtube_channels
                (user_id, google_account_id, youtube_channel_id, title, handle, custom_url, thumbnail_url, subscriber_count, video_count, view_count, status)
             VALUES
                (:user_id, :google_account_id, :youtube_channel_id, :title, :handle, :custom_url, :thumbnail_url, :subs, :videos, :views, "active")'
        );
        $stmt->execute([
            'user_id' => $data['user_id'],
            'google_account_id' => $data['google_account_id'],
            'youtube_channel_id' => $data['youtube_channel_id'],
            'title' => $data['title'],
            'handle' => $data['handle'] ?? null,
            'custom_url' => $data['custom_url'] ?? null,
            'thumbnail_url' => $data['thumbnail_url'] ?? null,
            'subs' => $data['subscriber_count'] ?? null,
            'videos' => $data['video_count'] ?? null,
            'views' => $data['view_count'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }
}