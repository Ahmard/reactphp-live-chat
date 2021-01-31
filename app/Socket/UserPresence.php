<?php


namespace App\Socket;


class UserPresence
{
    private static array $trackers = [];

    public static function iamOnline(int $userId): void
    {
        static::updatePresence($userId, 'online');
    }

    private static function updatePresence(int $userId, string $presence = 'offline'): void
    {
        $myTrackers = static::$trackers[$userId] ?? [];

        foreach ($myTrackers as $myTracker) {
            call_user_func($myTracker, $userId, $presence);
        }
    }

    public static function iamOffline(int $userId): void
    {
        static::updatePresence($userId, 'offline');
    }

    public static function track(int $currentUserId, int $userId, callable $callback): void
    {
        static::$trackers[$userId][$currentUserId] = $callback;
    }

    public static function removeTracker(int $currentUserId, int $userId): void
    {
        $userTrackers = self::$trackers[$currentUserId] ?? [];
        if (count($userTrackers) == 1) {
            unset(self::$trackers[$currentUserId]);
        } else {
            unset(self::$trackers[$userId][$currentUserId]);
        }
    }
}