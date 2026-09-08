<?php
declare(strict_types=1);
namespace App\Services\Drive;

use App\Services\Google\GoogleTokenService;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;

final class GoogleDriveService {
    private const ROOT_FOLDER_NAME = 'YouTube Content Manager';
    private const CHANNEL_SUBFOLDERS = [
        'tiktok_downloads' => 'TikTok Downloads',
        'ready_to_upload'  => 'Ready To Upload',
        'scheduled'        => 'Scheduled',
        'uploaded'         => 'Uploaded',
        'failed'           => 'Failed',
        'drafts'           => 'Drafts',
    ];

    public function __construct(private readonly GoogleTokenService $tokens) {}
    public static function make(): self { return new self(GoogleTokenService::make()); }

    private function driveClient(array $googleAccount): Drive {
    return new Drive($this->tokens->driveClientFor($googleAccount));
}

    public function createFolder(array $googleAccount, string $name, ?string $parentDriveId = null): string {
        $drive = $this->driveClient($googleAccount);
        $file = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => $parentDriveId ? [$parentDriveId] : [],
        ]);
        return $drive->files->create($file, ['fields' => 'id'])->getId();
    }

    public function ensureRootFolder(array $googleAccount): string {
        $drive = $this->driveClient($googleAccount);
        $result = $drive->files->listFiles([
            'q' => "name = '" . self::ROOT_FOLDER_NAME . "' and mimeType = 'application/vnd.google-apps.folder' and trashed = false",
            'fields' => 'files(id,name)',
            'pageSize' => 1,
        ]);
        $existing = $result->getFiles();
        return !empty($existing) ? $existing[0]->getId() : $this->createFolder($googleAccount, self::ROOT_FOLDER_NAME);
    }

    /** @return array<string,string> folder_type => google_drive_folder_id */
    public function ensureChannelFolders(array $googleAccount, string $rootFolderId, string $channelName): array {
        $channelFolderId = $this->createFolder($googleAccount, $channelName, $rootFolderId);
        $result = ['root' => $channelFolderId];
        foreach (self::CHANNEL_SUBFOLDERS as $type => $label) {
            $result[$type] = $this->createFolder($googleAccount, $label, $channelFolderId);
        }
        return $result;
    }
}