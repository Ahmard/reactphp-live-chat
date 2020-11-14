<?php


namespace App\Socket;


class UserPresence
{
    private static array $trackers = [];

    private static function updatePresence(int $userId, string $presence = 'offline')
    {
        $myTrackers = static::$trackers[$userId] ?? [];

        foreach ($myTrackers as $myTracker){
            call_user_func($myTracker, $userId, $presence);
        }
    }

    public static function iamOnline(int $userId)
    {
        static::updatePresence($userId, 'online');
    }

    public static function iamOffline(int $userId)
    {
        static::updatePresence($userId, 'offline');
    }

    public static function track(int $currentUserId, int $userId, callable $callback)
    {
        static::$trackers[$userId][$currentUserId] = $callback;
    }

    public static function removeTracker(int $currentUserId, int $userId)
    {
        $userTrackers = self::$trackers[$currentUserId] ?? [];
        if (count($userTrackers) == 1){
            unset(self::$trackers[$currentUserId]);
        }else{
            unset(self::$trackers[$userId][$currentUserId]);
        }
    }
}