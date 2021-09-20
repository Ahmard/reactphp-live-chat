<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
use Server\Http\Request;
use Throwable;

class NoteController extends Controller
{
    public function index(): Response
    {
        return $this->response->view('user/note');
    }

    public function add(Request $request): PromiseInterface
    {
        $postData = $request->getParsedBody();
        return Connection::get()->query(
            'INSERT INTO notes(user_id, category_id, title, note) VALUES (?, ?, ?, ?)',
            [$request->auth()->userId(), $postData['category_id'], $postData['title'], $postData['note']]
        )
            ->then(function (Result $result) use (&$postData) {
                $postData['id'] = $result->insertId;
                return $this->response->json([
                    'status' => true,
                    'data' => $postData
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function view(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query('SELECT * FROM notes WHERE id = ? AND user_id = ?', [$params['id'], $request->auth()->userId()])
            ->then(function (Result $result) {
                return $this->response->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function list(Request $request): PromiseInterface
    {
        return Connection::get()
            ->query('SELECT * FROM notes WHERE user_id = ?', [$request->auth()->userId()])
            ->then(function (Result $result) {
                return $this->response->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function update(Request $request, array $params): PromiseInterface
    {
        $postData = $request->getParsedBody();
        $dbParams = [
            $postData['title'],
            $postData['note'],
            time(),
            $params['id'],
            $request->auth()->userId()
        ];
        return Connection::get()->query(
            'UPDATE notes SET title = ?, note = ?, updated_at = ? WHERE id = ? AND user_id = ?',
            $dbParams
        )
            ->then(function (Result $result) {
                return $this->response->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function move(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            'UPDATE notes SET category_id = ?, updated_at = ? WHERE id = ? AND user_id = ?;',
            [$params['catId'], time(), $params['noteId'], $request->auth()->userId()]
        )->then(function () {
            return $this->response->json([
                'status' => true,
                'message' => 'Note moved successfully.'
            ]);
        })->otherwise(function (Throwable $throwable) {
            return $this->response->json([
                'status' => false,
                'message' => 'Moving failed'
            ]);
        });
    }

    public function delete(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query('DELETE FROM notes WHERE id = ? AND user_id = ?', [$params['id'], $request->auth()->userId()])
            ->then(function (Result $result) {
                return $this->response->json([
                    'status' => true,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }
}