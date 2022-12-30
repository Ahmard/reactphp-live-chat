<?php


namespace App\Websocket;


use Evenement\EventEmitter;
use React\EventLoop\Loop;
use Server\Websocket\ConnectionInterface;

class UserPresence
{
    private static EventEmitter $emitter;

    /**
     * @var int[] $users
     */
    private static array $users = [];


    public static function initialize(): void
    {
        self::$emitter = new EventEmitter();

        // Track users availability
        Loop::addPeriodicTimer(1.0, function () {
            foreach (self::$users as $userId => $connId) {
                if (!Clients::exists($connId)) {
                    self::remove($userId);
                }
            }
        });
    }

    public static function add(int $connId, int $userId): void
    {
        self::$users[$userId] = $connId;
        self::$emitter->emit("user.online.$userId", [$connId]);
    }

    public static function remove(int $userId): void
    {
        unset(self::$users[$userId]);
        self::$emitter->emit("user.offline.$userId", [$userId]);
    }

    public static function get(int $userId): ?int
    {
        return self::$users[$userId] ?? null;
    }

    public static function isOnline(int $userId): bool
    {
        return array_key_exists($userId, self::$users);
    }

    public static function getConnection(int $userId): ?ConnectionInterface
    {
        $connId = self::get($userId);
        return $connId ? Clients::get($connId) : null;
    }

    public static function track(int $userId, callable $callback): void
    {
        self::$emitter->on("user.online.$userId", $callback);
        self::$emitter->on("user.offline.$userId", $callback);
    }
}