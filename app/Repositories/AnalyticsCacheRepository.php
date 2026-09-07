<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;

final class AnalyticsCacheRepository {
    public function __construct(private readonly \PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }
    // TODO: Implement repository operations for analytics_cache in the phase where its feature is activated.
}
