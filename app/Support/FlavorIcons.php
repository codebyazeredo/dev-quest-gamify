<?php

namespace App\Support;

/**
 * The subset of <x-icon> names that make sense as a "flavor" pick for an
 * achievement or title — excludes purely functional/UI icons (pencil, trash,
 * check, alert) that exist for buttons and toasts, not for representing an
 * in-game reward.
 */
class FlavorIcons
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return ['star', 'trophy', 'flag', 'fire', 'bug', 'rocket', 'bolt', 'medal'];
    }
}
