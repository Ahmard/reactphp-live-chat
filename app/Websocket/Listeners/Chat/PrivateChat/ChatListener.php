<?php


namespace App\Websocket\Listeners\Chat\PrivateChat;


use App\Websocket\Clients;
use App\Websocket\Listeners\Listener;
use App\Websocket\UserPresence;
use Clue\React\SQLite\Result;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
use Server\Websocket\ConnectionInterface;
use Server\Websocket\Request;
use Throwable;

class ChatListener extends Listener
{
    /**
     * @var ConnectionInterface[]
     */
    public static array $users = [];

    private int $typingStatusTimeout;


    public function __construct()
    {
        $this->typingStatusTimeout = $_ENV['PRIVATE_CHAT_TYPING_STATUS_TIMEOUT'];
    }


    public function iamOnline(Request $request): void
    {
        //Add to online list
        Clients::add($request);

        //Let his trackers know he's online
        UserPresence::add(
            connId: $request->client()->getConnectionId(),
            userId: $request->auth()->userId()
        );
    }

    public function monitorUsersPresence(Request $request): void
    {
        $message = $request->payload()->message;
        $users = $message->users ?? [];

        foreach ($users as $userTrackingData) {
            if (isset($userTrackingData->user_id)) {
                UserPresence::track(
                    $userTrackingData->user_id,
                    function ($trackedUserPresence, $trackedUserId) use ($request) {
                        $command = 'chat.private.offline';
                        if ('online' == $trackedUserPresence) {
                            $command = 'chat.private.online';
                        }

                        resp($request->client())->send($command, [
                            'user_id' => $trackedUserId
                        ]);
                    }
                );
            }
        }
    }

    /**
     * @param Request $request
     * @return bool|PromiseInterface
     */
    public function send(Request $request): PromiseInterface|bool
    {
        $userId = $request->auth()->userId();
        $payload = $request->payload();
        $receiverId = $payload->message->receiver_id;

        if (empty(trim($payload->message->message))) {
            return true;
        }

        $plainSql = 'SELECT conversers FROM messages WHERE (sender_id = ? AND receiver_id =?) OR (sender_id = ? AND receiver_id = ?)';
        return Connection::get()->query($plainSql, [$userId, $receiverId, $receiverId, $userId])
            ->then(function (Result $result) use ($userId, $payload, $request) {
                if (!empty($result->rows)) {
                    $conversers = $result->rows[0]['conversers'];
                } else {
                    $conversers = "{$userId} {$payload->message->receiver_id}";
                }

                //Send Message
                $sql = "INSERT INTO messages(sender_id, receiver_id, message, conversers) VALUES (?, ?, ?, ?)";
                $userId = $request->auth()->userId();
                return Connection::get()->query($sql, [$userId, $payload->message->receiver_id, $payload->message->message, $conversers])
                    ->then(function (Result $result) use ($payload, $request) {
                        if (UserPresence::isOnline($payload->message->receiver_id)) {
                            $client = UserPresence::getConnection($payload->message->receiver_id);
                            resp($client)->send('chat.private.send', [
                                'id' => $result->insertId,
                                'client_id' => $client->getConnectionId(),
                                'sender_id' => $request->auth()->userId(),
                                'time' => time(),
                                'message' => $payload->message->message,
                            ]);
                        }
                    })->otherwise(function (Throwable $throwable) use ($request) {
                        resp($request->client())->send('chat.private.error', $throwable);
                    });
            });
    }

    public function typing(Request $request): void
    {
        $userId = $request->auth()->userId();
        $payload = $request->payload();
        $receiverId = $payload->message->receiver_id;

        if (UserPresence::isOnline($receiverId)) {

            $client = UserPresence::getConnection($receiverId);

            $data = [
                'client_id' => $client->getConnectionId(),
                'sender_id' => $userId,
                'status' => 'typing',
                'timeout' => $this->typingStatusTimeout,
            ];

            //Let's see if user is typing or stopped typing
            if ($request->payload()->message->status !== 'typing') {
                $data['status'] = 'stopped';
            }

            resp($client)->send('chat.private.typing', $data);
        }
    }
}