<?php
declare(strict_types=1);
namespace App\Services\Settings;

use App\Core\Env;
use App\Repositories\SystemSettingRepository;

/**
 * Database-backed application settings.
 *
 * Non-secret settings (branding, defaults, timezone, etc.) live in the
 * `system_settings` table and are editable from /settings.
 *
 * Secrets (Google credentials, cron secret) always come from .env and are
 * never written to the database or echoed in full to the frontend, per the
 * Phase 1 security requirement.
 */
final class SettingsService {
    /** Keys that are safe to store in the database and edit from the UI. */
    private const DB_KEYS = [
        'app_name'        => 'Multi-Channel Content Manager',
        'app_url'         => '',
        'app_timezone'    => 'UTC',
        'logo_path'       => '',
        'favicon_path'    => '',
        'color_primary'   => '#12182a',
        'color_secondary' => '#d946ef',
        'color_accent'    => '#ef4444',
        'drive_root_folder_id'   => '',
        'default_upload_privacy' => 'private',
        'default_upload_category' => '',
        'default_upload_tags'     => '',
    ];

    public function __construct(private readonly SystemSettingRepository $repository) {}

    public static function make(): self {
        return new self(SystemSettingRepository::make());
    }

    /**
     * All database-backed settings merged with their defaults.
     */
    public function all(): array {
        $stored = $this->repository->all();
        $out = [];
        foreach (self::DB_KEYS as $key => $default) {
            $out[$key] = $stored[$key] ?? $default;
        }
        return $out;
    }

    public function get(string $key): ?string {
        if (!array_key_exists($key, self::DB_KEYS)) {
            return null;
        }
        return $this->repository->get($key, self::DB_KEYS[$key]);
    }

    /**
     * Persist only the recognised, non-secret keys. Anything else is ignored
     * so a form can never accidentally write a secret into the database.
     */
    public function save(array $input): void {
        $pairs = [];
        foreach (self::DB_KEYS as $key => $default) {
            if (array_key_exists($key, $input)) {
                $pairs[$key] = (string) $input[$key];
            }
        }
        $this->repository->setMany($pairs, false);
    }

    /**
     * Read-only view of the .env-managed secrets, masked for display.
     * TODO: Wire real "configured" checks to the Google/Cron services once
     * those features are implemented in their designated phases.
     */
    public function envSecretsStatus(): array {
        return [
            'google_client_id'     => $this->mask(Env::get('GOOGLE_CLIENT_ID')),
            'google_client_secret' => $this->mask(Env::get('GOOGLE_CLIENT_SECRET')),
            'google_redirect_uri'  => Env::get('GOOGLE_REDIRECT_URI', ''),
            'cron_secret'          => $this->mask(Env::get('CRON_SECRET')),
        ];
    }

    private function mask(?string $value): string {
        if ($value === null || $value === '') {
            return 'Not configured';
        }
        return 'Configured (' . str_repeat('•', 8) . ')';
    }
}
