<?php

namespace App\Entity;


class GovernorStatus {
    const STATUS_ACTIVE = 'active';
    const STATUS_UNKNOWN = 'unknown';
    const STATUS_TAGLESS = 'tagless';
    const STATUS_QUIT = 'quit';
    const STATUS_MIGRATED = 'migrated';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_BLACKLISTED = 'blacklisted';

    const DISPLAY_STATUS_ACTIVE = 'active';
    const DISPLAY_STATUS_INACTIVE = 'inactive';
    const DISPLAY_STATUS_BLACKLISTED = 'blacklisted';

    const GOV_STATUSES = [
        self::STATUS_ACTIVE,
        self::STATUS_UNKNOWN,
        self::STATUS_TAGLESS,
        self::STATUS_QUIT,
        self::STATUS_ARCHIVED,
        self::STATUS_MIGRATED,
        self::STATUS_BLACKLISTED
    ];

    public static function getDisplayStatus(string $status): string
    {
        if ($status === self::STATUS_ACTIVE) {
            return self::DISPLAY_STATUS_ACTIVE;
        }
        
        if ($status === self::STATUS_BLACKLISTED) {
            return self::DISPLAY_STATUS_BLACKLISTED;
        }

        return self::DISPLAY_STATUS_INACTIVE;
    }

    public static function getFormChoices(): array
    {
        $statusChoices = [];
        foreach (self::GOV_STATUSES as $status) {
            $statusChoices[$status] = $status;
        }

        return $statusChoices;
    }
}


