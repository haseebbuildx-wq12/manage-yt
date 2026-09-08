<?php
declare(strict_types=1);
namespace App\Interfaces;
interface GoogleTokenServiceInterface {
    public function getValidAccessToken(int $googleAccountId): string;
    public function isAccessTokenExpired(int $googleAccountId): bool;
    public function refreshAccessToken(int $googleAccountId): string;
    public function invalidateConnection(int $googleAccountId): void;
}
