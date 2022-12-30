<?php


namespace App\Websocket;


use Evenement\EventEmitter;
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

        event()->on('chat.private.user-left', function (ConnectionInterface $connection) {
            foreach (self::$users as $userId => $connId) {
                if ($connId == $connection->getConnectionId()) {
                    self::remove($userId);
                    break;
                }
            }

            console(true)->comment('private connection closed: ' . $connection->getConnectionId());
        });
    }

    public static function add(int $connId, int $userId): void
    {
        self::$users[$userId] = $connId;
        self::$emitter->emit("user.online.$userId", [$userId]);
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
        self::$emitter->on("user.online.$userId", fn(int $user) => $callback('online', $userId));
        self::$emitter->on("user.offline.$userId", fn(int $user) => $callback('offline', $userId));
    }
}