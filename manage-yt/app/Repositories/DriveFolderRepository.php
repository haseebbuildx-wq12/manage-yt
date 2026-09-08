<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;

final class DriveFolderRepository {
    public function __construct(private readonly \PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }
    // TODO: Implement repository operations for drive_folder in the phase where its feature is activated.
}
