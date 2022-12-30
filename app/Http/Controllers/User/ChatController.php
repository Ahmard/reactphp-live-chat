<?php


namespace App\Http\Controllers\User;

//aaad Ahmad
use App\Http\Controllers\Controller;
use App\Websocket\Model;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
use Server\Http\Request;
use Throwable;

class ChatController extends Controller
{

    public function index(): Response
    {
        return $this->response->view('user/chat/index');
    }

    public function privateChat(): Response
    {
        return $this->response->view('user/chat/private', [
            'socket_prefix' => $_ENV['PRIVATE_CHAT_SOCKET_URL_PREFIX']
        ]);
    }

    public function checkUser(Request $request): PromiseInterface
    {
        $username = $request->getQueryParams()['username'] ?? null;
        $userId = $this->request->auth()->userId();

        dump($userId);

        return Connection::get()->query("SELECT id, username FROM users WHERE id <> ? AND username LIKE '%a%'", [$userId, $username])
            ->then(function (Result $result) {
                if (!empty($result->rows)) {
                    return $this->response->jsonSuccess([
                        'exists' => true,
                        'user' => $result->rows
                    ]);
                }

                return $this->response->jsonSuccess([
                    'exists' => false
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->jsonError($throwable->getMessage());
            });
    }

    public function fetchConversations(): PromiseInterface
    {
        $userId = $this->request->auth()->userId();
        $sql = '
            SELECT users.username AS receiver_uname, 
                   userx.username AS sender_uname, 
                   messages.sender_id, 
                   messages.receiver_id, 
                   messages.conversers AS converserx,
                   COUNT(messages.id) AS total_messages
            FROM messages 
            JOIN users ON users.id = messages.receiver_id
            JOIN users AS userx ON userx.id = messages.sender_id
            WHERE (messages.sender_id = ? OR messages.receiver_id = ?)
            GROUP BY converserx
            ORDER BY (
                SELECT messages.created_at 
                FROM messages 
                WHERE conversers=converserx 
                ORDER BY id 
                DESC LIMIT 1
            )
        ';
        return Connection::create()->query($sql, [$userId, $userId])
            ->then(function (Result $result) {
                return $this->response->jsonSuccess([
                    'conversations' => $result->rows
                ]);
            })->otherwise(function (Throwable $throwable) {
                return $this->response->jsonError($throwable->getMessage());
            });
    }

    public function getConversationStatus(Request $request, array $params): PromiseInterface
    {
        $userId = $this->request->auth()->userId();
        return Connection::get()->query(
            'SELECT COUNT(*) FROM messages WHERE (sender_id = ? AND receiver_id = ?) AND status = 0;',
            [$params['id'], $userId]
        )->then(function (Result $result) use ($params) {
            return $this->response->jsonSuccess([
                'presence' => Model::exists($params['id']),
                'total_unread' => $result->rows[0]['COUNT(*)']
            ]);
        })->otherwise(function (Throwable $throwable) {
            return $this->response->jsonError($throwable->getMessage());
        });
    }

    public function fetchMessages(Request $request, array $params): PromiseInterface
    {
        $userId = $this->request->auth()->userId();
        $sql = "SELECT * FROM messages WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)";
        return Connection::get()->query($sql, [$userId, $params['id'], $params['id'], $userId])
            ->then(function (Result $result) {
                return $this->response->jsonSuccess($result->rows);
            })->otherwise(function (Throwable $throwable) {
                return $this->response->jsonError($throwable->getMessage());
            });
    }

    public function send(Request $request, array $params): PromiseInterface
    {
        $postedData = $request->getParsedBody();
        $userId = $this->request->auth()->userId();

        $plainSql = 'SELECT conversers FROM messages WHERE (sender_id = ? AND receiver_id =?) OR (sender_id = ? OR receiver_id = ?)';
        return Connection::get()->query($plainSql, [$userId, $params['id'], $params['id'], $userId])->then(function (Result $result) use ($userId, $params, $postedData) {
            if (!empty($result->rows)) {
                $conversers = $result->rows[0]['conversers'];
            } else {
                $conversers = "{$userId} {$params['id']}";
            }

            //Send Message
            $sql = "INSERT INTO messages(sender_id, receiver_id, message, conversers) VALUES (?, ?, ?, ?)";
            return Connection::get()->query($sql, [$this->request->auth()->userId(), $params['id'], $postedData['message'], $conversers])
                ->then(function (Result $result) use ($postedData) {
                    $postedData['id'] = $result->insertId;
                    $postedData['time'] = time();
                    return $this->response->jsonSuccess($postedData);
                })->otherwise(function (Throwable $throwable) {
                    return $this->response->jsonError($throwable->getMessage());
                });
        });

    }

    public function markAsRead(Request $request, array $params): PromiseInterface
    {
        $plainSql = 'UPDATE messages SET status = ? WHERE id = ?';
        return Connection::get()->query($plainSql, [1, $params['id']])->then(function () {
            return $this->response->jsonSuccess(true);
        })->otherwise(function (Throwable $throwable) {
            return $this->response->jsonError($throwable->getMessage());
        });
    }
}