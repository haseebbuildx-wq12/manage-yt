<?php
declare(strict_types=1);
namespace App\Providers\TikTok;
use App\Interfaces\TikTokProviderInterface;
final class TikTokProvider implements TikTokProviderInterface {
    public function discover(string $profileUrl, array $options = []): array {
        // TODO: Implement only authorized/user-owned content import in Phase 4.
        return [];
    }
}
