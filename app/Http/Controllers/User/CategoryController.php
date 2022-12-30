<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
use Server\Http\Request;
use Throwable;

class CategoryController extends Controller
{
    protected string $categoryDBTable;
    protected string $dataDBTable;


    public function __construct(array $objects)
    {
        parent::__construct($objects);

        $this->dataDBTable = $this->request
            ->getDispatchResult()
            ->getRoute()
            ->getFields()['dbTable'];

        $this->categoryDBTable = $this->dataDBTable == 'notes'
            ? 'note_categories'
            : 'list_categories';
    }

    public function add(Request $request): PromiseInterface
    {
        $data = $request->getParsedBody();

        return Connection::get()->query(
            "INSERT INTO $this->categoryDBTable (name, user_id) VALUES (?, ?);",
            [$data['name'], $request->auth()->userId()]
        )->then(function (Result $result) use (&$data) {
            $data['id'] = $result->insertId;
            return $this->response->jsonSuccess($data);
        })->otherwise(function () {
            return $this->response->jsonError('Insertion failed');
        });
    }

    public function list(Request $request): PromiseInterface
    {
        return Connection::get()
            ->query("SELECT * FROM $this->categoryDBTable WHERE user_id = ?;", [$request->auth()->userId()])
            ->then(function (Result $result) {
                return $this->response->jsonSuccess($result->rows);
            })->otherwise(function (Throwable $throwable) {
                return $this->response->jsonError('List failed');
            });
    }

    public function open(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            "SELECT * FROM $this->dataDBTable WHERE category_id = ? AND user_id = ?;",
            [$params['id'], $request->auth()->userId()]
        )->then(function (Result $result) {
            return $this->response->jsonSuccess($result->rows);
        })->otherwise(function () {
            return $this->response->jsonError('Selection failed');
        });
    }

    public function rename(Request $request, array $params): PromiseInterface
    {
        $data = $request->getParsedBody();
        return Connection::get()->query(
            "UPDATE $this->categoryDBTable SET name = ?, updated_at = ? WHERE id = ? AND user_id = ?;",
            [$data['name'], time(), $params['id'], $request->auth()->userId()]
        )->then(function (Result $result) {
            return $this->response->jsonSuccess($result->rows);
        })->otherwise(function () {
            return $this->response->jsonError('Renaming failed');
        });
    }

    public function delete(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            "DELETE FROM $this->categoryDBTable WHERE id = ? AND user_id = ?;",
            [$params['id'], $request->auth()->userId()]
        )->then(function (Result $result) {
            return $this->response->jsonSuccess($result->rows);
        })->otherwise(function () {
            return $this->response->jsonError('Deletion failed');
        });
    }
}