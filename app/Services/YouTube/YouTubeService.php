<?php
declare(strict_types=1);
namespace App\Services\YouTube;

use App\Services\Google\GoogleTokenService;
use Google\Service\YouTube;

final class YouTubeService {
    public function __construct(private readonly GoogleTokenService $tokens) {}
    public static function make(): self { return new self(GoogleTokenService::make()); }

    public function listChannelsForAccount(array $googleAccount): array {
        $client = $this->tokens->clientFor($googleAccount);
        $youtube = new YouTube($client);
        $response = $youtube->channels->listChannels('snippet,statistics', ['mine' => true, 'maxResults' => 50]);

        $channels = [];
        foreach ($response->getItems() as $item) {
            $snippet = $item->getSnippet();
            $stats = $item->getStatistics();
            $channels[] = [
                'id' => $item->getId(),
                'title' => $snippet->getTitle(),
                'customUrl' => $snippet->getCustomUrl(),
                'thumbnail' => $snippet->getThumbnails()?->getDefault()?->getUrl(),
                'subscriberCount' => $stats && !$stats->getHiddenSubscriberCount() ? (int) $stats->getSubscriberCount() : null,
                'videoCount' => $stats ? (int) $stats->getVideoCount() : null,
                'viewCount' => $stats ? (int) $stats->getViewCount() : null,
            ];
        }
        return $channels;
    }
}