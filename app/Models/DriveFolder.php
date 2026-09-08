<?php
declare(strict_types=1);
namespace App\Models;

final class DriveFolder {
    public function __construct(public array $attributes = []) {}
    public function __get(string $name): mixed { return $this->attributes[$name] ?? null; }
}