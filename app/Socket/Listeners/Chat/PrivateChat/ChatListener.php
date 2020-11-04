<?php


namespace App\Socket\Listeners\Chat\PrivateChat;


use App\Core\Database\Connection;
use App\Core\Socket\ConnectionInterface;
use App\Core\Socket\Request;
use App\Models\Client;
use App\Socket\Listeners\Listener;
use Clue\React\SQLite\Result;
use Throwable;

class ChatListener extends Listener
{
    /**
     * @var ConnectionInterface[]
     */
    public static array $users = [];

    public function iamOnline(Request $request)
    {
        Client::add($request->auth()->userId(), $request->client());
    }

    public function send(Request $request)
    {
        $userId = $request->auth()->userId();
        $payload = $request->payload();
        $receiverId = $payload->receiver_id;

        if(Client::exists($receiverId)){
            resp(Client::get($receiverId))->send('chat.private.send', [
                'sender_id' => $userId,
                'message' => $payload->message,
            ]);
        }

        $plainSql = 'SELECT conversers FROM messages WHERE (sender_id = ? AND receiver_id =?) OR (sender_id = ? OR receiver_id = ?)';
        return Connection::get()->query($plainSql, [$userId, $receiverId, $receiverId, $userId])
            ->then(function (Result $result) use ($userId, $payload, $request) {
                if (!empty($result->rows)) {
                    $conversers = $result->rows[0]['conversers'];
                } else {
                    $conversers = "{$userId} {$payload->receiver_id}";
                }

                //Send Message
                $sql = "INSERT INTO messages(sender_id, receiver_id, message, conversers, time) VALUES (?, ?, ?, ?, ?)";
                $userId = $request->auth()->userId();
                return Connection::get()->query($sql, [$userId, $payload->receiver_id, $payload->message, $conversers, time()])
                    ->then(function (Result $result) use ($payload, $request) {
                        $payload->id = $result->insertId;
                        $payload->time = time();
                    })->otherwise(function (Throwable $throwable) use ($request) {
                        resp($request->client())->send('chat.private.error', $throwable);
                    });
            });
    }
}