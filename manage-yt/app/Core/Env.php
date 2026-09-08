<?php
declare(strict_types=1);

namespace App\Core;

final class Env {
    public static function load(string $path): void {
        if (!is_file($path)) return;
        foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
            [$key, $value] = explode('=', $line, 2);
            $value = trim($value);
            if (($value[0] ?? '') === '"' && str_ends_with($value, '"')) $value = substr($value, 1, -1);
            $_ENV[trim($key)] = $value;
            $_SERVER[trim($key)] = $value;
        }
    }
    public static function get(string $key, ?string $default = null): ?string {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }
}
