<?php
declare(strict_types=1);
function e(?string $value): string { return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8'); }
function env(string $key, ?string $default = null): ?string { return App\Core\Env::get($key, $default); }
