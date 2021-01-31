<?php


namespace App\Http\Controllers\User;


use App\Core\Database\Connection;
use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Throwable;

class CategoryController extends Controller
{
    public function add(ServerRequestInterface $request): PromiseInterface
    {
        $data = $request->getParsedBody();

        return Connection::get()->query(
            'INSERT INTO categories(name, user_id, created_at, updated_at) VALUES (?, ?, ?, ?);',
            [$data['name'], request()->auth()->userId(), time(), time()]
        )->then(function (Result $result) use (&$data) {
            $data['id'] = $result->insertId;
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        })->otherwise(function () {
            return response()->json([
                'status' => false,
                'message' => 'Insertion failed'
            ]);
        });
    }

    public function list(): PromiseInterface
    {
        return Connection::get()
            ->query('SELECT * FROM categories WHERE user_id = ?;', [request()->auth()->userId()])
            ->then(function (Result $result) {
                return response()->json([
                    'status' => true,
                    'data' => $result->rows
                ]);
            })->otherwise(function (Throwable $throwable) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selection failed'
                ]);
            });
    }

    public function open(ServerRequestInterface $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            'SELECT * FROM notes WHERE category_id = ? AND user_id = ?;',
            [$params['id'], request()->auth()->userId()]
        )->then(function (Result $result) {
            return response()->json([
                'status' => true,
                'data' => $result->rows
            ]);
        })->otherwise(function () {
            return response()->json([
                'status' => false,
                'message' => 'Selection failed'
            ]);
        });
    }

    public function rename(ServerRequestInterface $request, array $params): PromiseInterface
    {
        $data = $request->getParsedBody();
        return Connection::get()->query(
            'UPDATE categories SET name = ?, updated_at = ? WHERE id = ? AND user_id = ?;',
            [$data['name'], time(), $params['id'], request()->auth()->userId()]
        )->then(function (Result $result) {
            return response()->json([
                'status' => true,
                'data' => $result->rows
            ]);
        })->otherwise(function () {
            return response()->json([
                'status' => false,
                'message' => 'Renaming failed'
            ]);
        });
    }

    public function delete(ServerRequestInterface $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            'DELETE FROM categories WHERE id = ? AND user_id = ?;',
            [$params['id'], request()->auth()->userId()]
        )->then(function (Result $result) use (&$data) {
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        })->otherwise(function () {
            return response()->json([
                'status' => false,
                'message' => 'Deletion failed'
            ]);
        });
    }
}