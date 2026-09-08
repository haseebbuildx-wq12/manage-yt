<?php
declare(strict_types=1);
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Encryption;
use App\Middleware\AuthMiddleware;
use App\Providers\Google\GoogleProvider;
use App\Repositories\GoogleAccountRepository;

final class GoogleController extends Controller {
public function connect(): void {
    AuthMiddleware::check();
    header('Location: ' . GoogleProvider::make()->oauth->getAuthUrl('step1'));
    exit;
}

public function callback(): void {
    AuthMiddleware::check();

    if (!empty($_GET['error'])) {
        header('Location: /channels?google_error=' . urlencode((string) $_GET['error']));
        exit;
    }
    $code = $_GET['code'] ?? null;
    $state = $_GET['state'] ?? 'step1';
    if (!$code) {
        header('Location: /channels?google_error=missing_code');
        exit;
    }

    try {
        $result = GoogleProvider::make()->oauth->handleCallback($code, $state);
    } catch (\Throwable $e) {
        header('Location: /channels?google_error=oauth_failed');
        exit;
    }

    $tokens = $result['tokens'];
    $repo = GoogleAccountRepository::make();
    $expiry = date('Y-m-d H:i:s', time() + (int) ($tokens['expires_in'] ?? 3600));
    $scopes = explode(' ', $tokens['scope'] ?? '');

if ($state === 'step1') {
    $profile = $result['profile'];
    $existing = $repo->findByUserAndGoogleId((int) $_SESSION['user_id'], $profile['id']);

    if ($existing) {
        $repo->updateDriveTokens((int) $existing['id'], Encryption::encrypt($tokens['access_token']),
            isset($tokens['refresh_token']) ? Encryption::encrypt($tokens['refresh_token']) : null, $expiry);
        $_SESSION['pending_google_account_id'] = $existing['id'];
    } else {
        if (empty($tokens['refresh_token'])) {
            header('Location: /channels?google_error=no_refresh_token');
            exit;
        }
        $id = $repo->create([
            'user_id' => $_SESSION['user_id'], 'google_account_id' => $profile['id'], 'email' => $profile['email'],
            'access_token_encrypted' => null,
            'refresh_token_encrypted' => null,
            'token_expiry' => $expiry, 'scopes' => $scopes, 'status' => 'connected',
        ]);
        $repo->updateDriveTokens($id, Encryption::encrypt($tokens['access_token']),
            Encryption::encrypt($tokens['refresh_token']), $expiry);
        $_SESSION['pending_google_account_id'] = $id;
    }

    header('Location: ' . GoogleProvider::make()->oauth->getAuthUrl('step2'));
    exit;
}

  

    // step2: YouTube scope grant ho gayi — cumulative token save karein.
    $accountId = (int) ($_SESSION['pending_google_account_id'] ?? 0);
    if ($accountId) {
        $repo->updateTokens($accountId, Encryption::encrypt($tokens['access_token']),
            isset($tokens['refresh_token']) ? Encryption::encrypt($tokens['refresh_token']) : null, $expiry, $scopes);
    }
    unset($_SESSION['pending_google_account_id']);

    header('Location: /channels/available');
    exit;
}

    public function disconnect(): void {
        AuthMiddleware::check();
        if (!Csrf::verify($_POST['_csrf'] ?? null)) { header('Location: /channels'); exit; }
        $id = (int) ($_POST['google_account_id'] ?? 0);
        $repo = GoogleAccountRepository::make();
        $account = $repo->find($id);
        if ($account && (int) $account['user_id'] === (int) $_SESSION['user_id']) {
            $repo->updateStatus($id, 'disconnected');
        }
        header('Location: /channels');
        exit;
    }
}