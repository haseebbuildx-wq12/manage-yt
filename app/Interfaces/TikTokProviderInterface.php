<?php
declare(strict_types=1);
namespace App\Interfaces;
interface TikTokProviderInterface {
    public function discover(string $profileUrl, array $options = []): array;
}
