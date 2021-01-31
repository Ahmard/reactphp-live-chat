<?php


namespace App\Http\Controllers\User;


use App\Core\Database\Connection;
use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Throwable;

class NoteController extends Controller
{
    public function index(): Response
    {
        return view('user/note');
    }

    public function add(ServerRequestInterface $request): PromiseInterface
    {
        $postData = $request->getParsedBody();
        return Connection::get()->query(
            'INSERT INTO notes(user_id, category_id, title, note, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)',
            [request()->auth()->userId(), $postData['category_id'], $postData['title'], $postData['note'], time(), time()]
        )
            ->then(function (Result $result) use (&$postData) {
                $postData['id'] = $result->insertId;
                return response()->json([
                    'status' => true,
                    'data' => $postData
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function view(ServerRequestInterface $request, array $params): PromiseInterface
    {
        return Connection::get()->query('SELECT * FROM notes WHERE id = ? AND user_id = ?', [$params['id'], request()->auth()->userId()])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function list(): PromiseInterface
    {
        return Connection::get()->query('SELECT * FROM notes WHERE user_id = ?', [request()->auth()->userId()])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function update(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $postData = $request->getParsedBody();
        return Connection::get()->query(
            'UPDATE notes SET title = ?, note = ?, updated_at = ? WHERE id = ? AND user_id = ?',
            [$postData['title'], $postData['note'], time(), $params['id'], request()->auth()->userId()]
        )
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => $result->rows,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }

    public function move(ServerRequestInterface $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            'UPDATE notes SET category_id = ?, updated_at = ? WHERE id = ? AND user_id = ?;',
            [$params['catId'], time(), $params['noteId'], request()->auth()->userId()]
        )->then(function () {
            return response()->json([
                'status' => true,
                'message' => 'Note moved successfully.'
            ]);
        })->otherwise(function (Throwable $throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Moving failed'
            ]);
        });
    }

    public function delete(ServerRequestInterface $request, array $params): PromiseInterface
    {
        return Connection::get()->query('DELETE FROM notes WHERE id = ? AND user_id = ?', [$params['id'], request()->auth()->userId()])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                ]);
            })
            ->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'data' => $throwable,
                ]);
            });
    }
}