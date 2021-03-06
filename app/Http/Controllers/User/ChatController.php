<?php


namespace App\Http\Controllers\User;


use App\Core\Database\Connection;
use App\Http\Controllers\Controller;
use App\Socket\UserStorage;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Throwable;

class ChatController extends Controller
{

    public function index(): Response
    {
        return view('user/chat/index');
    }

    public function privateChat(): Response
    {
        return view('user/chat/private', [
            'socket_prefix' => $_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX']
        ]);
    }

    public function checkUser(ServerRequestInterface $request): PromiseInterface
    {
        $username = $request->getQueryParams()['username'] ?? null;
        $userId = request()->auth()->userId();

        return Connection::get()->query('SELECT id, username FROM users WHERE username = ? AND id != ?', [$username, $userId])
            ->then(function (Result $result) {
                if (!empty($result->rows)) {
                    $userData = $result->rows[0];

                    //Remove sensitive data
                    unset($userData['password']);
                    unset($userData['token']);

                    return response()->json([
                        'status' => true,
                        'exists' => true,
                        'data' => $userData
                    ]);
                }

                return response()->json([
                    'status' => true,
                    'exists' => false
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => true,
                    'error' => $throwable
                ]);
            });
    }

    public function fetchConversations(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $userId = request()->auth()->userId();
        $sql = '
            SELECT users.username AS receiver_uname, userx.username AS sender_uname, messages.sender_id, messages.receiver_id, messages.conversers AS converserx
            FROM messages 
            JOIN users ON users.id = messages.receiver_id
            JOIN users AS userx ON userx.id = messages.sender_id
            WHERE (messages.sender_id = ? OR messages.receiver_id = ?)
            GROUP BY converserx
            ORDER BY (
                SELECT time 
                FROM messages 
                WHERE conversers=converserx 
                ORDER BY id 
                DESC LIMIT 1
            )
        ';
        return Connection::create()->query($sql, [$userId, $userId])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => [
                        'conversations' => $result->rows
                    ]
                ]);
            })->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'error' => $throwable->getMessage()
                ]);
            });
    }

    public function getConversationStatus(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $userId = request()->auth()->userId();
        return Connection::get()->query(
            'SELECT COUNT(*) FROM messages WHERE (sender_id = ? AND receiver_id = ?) AND status = 0;',
            [$params['id'], $userId]
        )->then(function (Result $result) use ($params) {
            return response()->json([
                'status' => true,
                'data' => [
                    'presence' => UserStorage::exists($params['id']),
                    'total_unread' => $result->rows[0]['COUNT(*)']
                ]
            ]);
        });
    }

    public function fetchMessages(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $userId = request()->auth()->userId();
        $sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
        return Connection::get()->query($sql, [$userId, $params['id'], $params['id'], $userId])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'error' => $throwable->getMessage()
                ]);
            });
    }

    public function send(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $postedData = $request->getParsedBody();
        $userId = request()->auth()->userId();

        $plainSql = 'SELECT conversers FROM messages WHERE (sender_id = ? AND receiver_id =?) OR (sender_id = ? OR receiver_id = ?)';
        return Connection::get()->query($plainSql, [$userId, $params['id'], $params['id'], $userId])->then(function (Result $result) use ($userId, $params, $postedData) {
            if (!empty($result->rows)) {
                $conversers = $result->rows[0]['conversers'];
            } else {
                $conversers = "{$userId} {$params['id']}";
            }

            //Send Message
            $sql = "INSERT INTO messages(sender_id, receiver_id, message, conversers, time) VALUES (?, ?, ?, ?, ?)";
            return Connection::get()->query($sql, [request()->auth()->userId(), $params['id'], $postedData['message'], $conversers, time()])
                ->then(function (Result $result) use ($postedData) {
                    $postedData['id'] = $result->insertId;
                    $postedData['time'] = time();
                    return response()->json([
                        'status' => true,
                        'data' => $postedData
                    ]);
                })->otherwise(function (Throwable $throwable) {
                    return response()->json([
                        'status' => false,
                        'error' => $throwable->getMessage()
                    ]);
                });
        });

    }

    public function markAsRead(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $plainSql = 'UPDATE messages SET status = ? WHERE id = ?';
        return Connection::get()->query($plainSql, [1, $params['id']])->then(function () {
            return response()->json([
                'status' => true,
            ]);
        })->otherwise(function (Throwable $throwable) {
            return response()->json([
                'status' => false,
                'error' => $throwable->getMessage()
            ]);
        });
    }
}