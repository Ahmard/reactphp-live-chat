<?php


namespace App\Http\Controllers\User;


use App\Core\Database\Connection;
use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

class CategoryController extends Controller
{
    public function add(ServerRequestInterface $request)
    {
        $data = $request->getParsedBody();

        return Connection::get()->query(
            'INSERT INTO categories(name, created_at, updated_at) VALUES (?, ?, ?);',
            [$data['name'], time(), time()]
        )->then(function (Result $result) use (&$data) {
            $data['id'] = $result->insertId;
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        })->otherwise(function (Throwable $throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Insertion failed'
            ]);
        });
    }

    public function list(ServerRequestInterface $request)
    {
        return Connection::get()->query('SELECT * FROM categories;')->then(function (Result $result) use (&$data) {
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

    public function open(ServerRequestInterface $request, array $params)
    {
        return Connection::get()->query(
            'SELECT * FROM notes WHERE category_id = ?;',
            [$params['id']]
        )->then(function (Result $result) use (&$data) {
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

    public function rename(ServerRequestInterface $request, array $params)
    {
        $data = $request->getParsedBody();
        return Connection::get()->query(
            'UPDATE categories SET name = ?, updated_at = ? WHERE id = ?;',
            [$data['name'], time(), $params['id']]
        )->then(function (Result $result) use (&$data) {
            return response()->json([
                'status' => true,
                'data' => $result->rows
            ]);
        })->otherwise(function (Throwable $throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Renaming failed'
            ]);
        });
    }

    public function delete(ServerRequestInterface $request, array $params)
    {
        return Connection::get()->query(
            'DELETE FROM categories WHERE id = ?;',
            [$params['id']]
        )->then(function (Result $result) use (&$data) {
            return response()->json([
                'status' => true,
                'data' => $data
            ]);
        })->otherwise(function (Throwable $throwable) {
            return response()->json([
                'status' => false,
                'message' => 'Deletion failed'
            ]);
        });
    }
}