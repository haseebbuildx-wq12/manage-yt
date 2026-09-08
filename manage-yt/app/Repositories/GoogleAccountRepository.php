<?php
declare(strict_types=1);
namespace App\Repositories;

use App\Core\Database;

final class GoogleAccountRepository {
    public function __construct(private readonly \PDO $db) {}
    public static function make(): self { return new self(Database::pdo()); }
    // TODO: Implement repository operations for google_account in the phase where its feature is activated.
}
