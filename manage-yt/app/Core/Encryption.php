<?php
declare(strict_types=1);

namespace App\Core;

final class Encryption {
    public static function encrypt(string $plaintext): string {
        $key = hash('sha256', (string) Env::get('APP_ENCRYPTION_KEY'), true);
        $iv = random_bytes(16);
        $cipher = openssl_encrypt($plaintext, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $cipher);
    }
    public static function decrypt(string $payload): string {
        $raw = base64_decode($payload, true);
        if ($raw === false || strlen($raw) < 17) throw new \RuntimeException('Invalid encrypted payload.');
        $key = hash('sha256', (string) Env::get('APP_ENCRYPTION_KEY'), true);
        $plain = openssl_decrypt(substr($raw, 16), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, substr($raw, 0, 16));
        if ($plain === false) throw new \RuntimeException('Unable to decrypt payload.');
        return $plain;
    }
}
