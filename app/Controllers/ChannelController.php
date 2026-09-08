<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Middleware\AuthMiddleware;
use App\Providers\Google\GoogleProvider;
use App\Repositories\GoogleAccountRepository;
use App\Repositories\YouTubeChannelRepository;
use App\Repositories\DriveFolderRepository;

final class ChannelController extends Controller {
    public function index(): void {
        AuthMiddleware::check();
        $userId = (int) $_SESSION['user_id'];
        $this->view('channels/index', [
            'title' => 'Channels',
            'channels' => YouTubeChannelRepository::make()->allForUser($userId),
            'googleAccounts' => GoogleAccountRepository::make()->allForUser($userId),
            'error' => $_GET['google_error'] ?? null,
        ], 'app');
    }

    public function available(): void {
        AuthMiddleware::check();
        $userId = (int) $_SESSION['user_id'];
        $accounts = GoogleAccountRepository::make()->allForUser($userId);
        $existingIds = array_column(YouTubeChannelRepository::make()->allForUser($userId), 'youtube_channel_id');

        $provider = GoogleProvider::make();
        $available = [];
        $accountError = null;

        foreach ($accounts as $account) {
            if ($account['status'] !== 'connected') continue;
            try {
                foreach ($provider->youtube->listChannelsForAccount($account) as $channel) {
                    if (in_array($channel['id'], $existingIds, true)) continue;
                    $channel['google_account_id'] = $account['id'];
                    $available[] = $channel;
                }
            } catch (\Throwable $e) {
                $accountError = 'Google se channels fetch nahi ho paye. Reconnect karein.';
            }
        }

        $this->view('channels/available', ['title' => 'Connect Channels', 'available' => $available, 'error' => $accountError], 'app');
    }

    public function connect(): void {
        AuthMiddleware::check();
        if (!Csrf::verify($_POST['_csrf'] ?? null)) { header('Location: /channels/available'); exit; }

        $selected = $_POST['channels'] ?? [];
        $userId = (int) $_SESSION['user_id'];
        $provider = GoogleProvider::make();
        $channelRepo = YouTubeChannelRepository::make();
        $folderRepo = DriveFolderRepository::make();
        $accountRepo = GoogleAccountRepository::make();

        foreach ($selected as $encoded) {
            [$googleAccountId, $channelId, $title] = array_pad(explode('|', $encoded, 3), 3, '');
            if ($channelRepo->findByYoutubeChannelId($channelId)) continue;

            $account = $accountRepo->find((int) $googleAccountId);
            if (!$account) continue;

            $localChannelId = $channelRepo->create([
                'user_id' => $userId, 'google_account_id' => $account['id'],
                'youtube_channel_id' => $channelId, 'title' => $title,
            ]);

            try {
                $rootFolderId = $provider->drive->ensureRootFolder($account);
                $folders = $provider->drive->ensureChannelFolders($account, $rootFolderId, $title);
                foreach ($folders as $type => $driveId) {
                    $folderRepo->create([
                        'channel_id' => $localChannelId, 'user_id' => $userId,
                        'google_drive_folder_id' => $driveId,
                        'name' => $type === 'root' ? $title : ucwords(str_replace('_', ' ', $type)),
                        'folder_type' => $type,
                    ]);
                }
            } catch (\Throwable $e) { /* channel saved, folders retryable later */ }
        }

        header('Location: /channels');
        exit;
    }
}