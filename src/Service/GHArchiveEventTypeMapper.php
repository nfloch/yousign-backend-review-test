<?php

namespace App\Service;

use App\Entity\EventType;

class GHArchiveEventTypeMapper
{
    public function isEventTypeAllowed(string $type): bool
    {
        $allowedTypes = [
            'CommitCommentEvent',
            'IssueCommentEvent',
            'PullRequestReviewCommentEvent',
            'PushEvent',
            'PullRequestEvent',
        ];

        return in_array($type, $allowedTypes);
    }

    /**
     * @return string The corresponding event type for entity
     *
     * @throws \RuntimeException
     */
    public function transformEventType(string $ghArchiveType): string
    {
        return match ($ghArchiveType) {
            'CommitCommentEvent', 'IssueCommentEvent', 'PullRequestReviewCommentEvent' => EventType::COMMENT,
            'PullRequestEvent' => EventType::PULL_REQUEST,
            'PushEvent' => EventType::COMMIT,
            default => throw new \RuntimeException('Unknown ghArchive type')
        };
    }
}
