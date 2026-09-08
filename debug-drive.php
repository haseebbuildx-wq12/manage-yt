<?php
require __DIR__ . '/app/bootstrap.php';

use App\Repositories\GoogleAccountRepository;
use App\Repositories\YouTubeChannelRepository;
use App\Providers\Google\GoogleProvider;
use App\Repositories\DriveFolderRepository;

$account = GoogleAccountRepository::make()->allForUser(1)[0] ?? null;
$channel = YouTubeChannelRepository::make()->allForUser(1)[0] ?? null;

if (!$account || !$channel) {
    die('Account ya channel record nahi mila.');
}

echo "Account: " . $account['email'] . " (status: " . $account['status'] . ")<br>";
echo "Channel: " . $channel['title'] . "<br><br>";

try {
    $provider = GoogleProvider::make();
    echo "Root folder banane ki koshish...<br>";
    $rootId = $provider->drive->ensureRootFolder($account);
    echo "Root folder ID: $rootId<br>";

    echo "Channel folders banane ki koshish...<br>";
    $folders = $provider->drive->ensureChannelFolders($account, $rootId, $channel['title']);
    echo "<pre>" . print_r($folders, true) . "</pre>";

    foreach ($folders as $type => $driveId) {
        DriveFolderRepository::make()->create([
            'channel_id' => $channel['id'],
            'user_id' => 1,
            'google_drive_folder_id' => $driveId,
            'name' => $type === 'root' ? $channel['title'] : ucwords(str_replace('_', ' ', $type)),
            'folder_type' => $type,
        ]);
    }
    echo "<br><strong>Success! Database mein bhi save ho gaya.</strong>";
} catch (\Throwable $e) {
    echo "<br><strong style='color:red;'>ERROR:</strong><br>";
    echo $e->getMessage();
    echo "<br><br><pre>" . $e->getTraceAsString() . "</pre>";
}