<?php
declare(strict_types=1);
namespace App\Providers\Google;

use App\Services\Google\GoogleOAuthService;
use App\Services\Google\GoogleTokenService;
use App\Services\YouTube\YouTubeService;
use App\Services\Drive\GoogleDriveService;

final class GoogleProvider {
    public function __construct(
        public readonly GoogleOAuthService $oauth,
        public readonly GoogleTokenService $tokens,
        public readonly YouTubeService $youtube,
        public readonly GoogleDriveService $drive,
    ) {}

    public static function make(): self {
        return new self(new GoogleOAuthService(), GoogleTokenService::make(), YouTubeService::make(), GoogleDriveService::make());
    }
}