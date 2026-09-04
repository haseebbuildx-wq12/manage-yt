<?php
namespace App\Services\TikTok;
interface TikTokProviderInterface { public function discover(string $profileUrl, array $options=[]): array; }
