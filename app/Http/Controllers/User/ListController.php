<?php


namespace App\Http\Controllers\User;


use App\Http\Controllers\Controller;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Server\Database\Connection;
use Server\Http\Request;
use Throwable;

class ListController extends Controller
{
    public function index(): Response
    {
        return $this->response->view('user/list');
    }

    public function add(Request $request): PromiseInterface
    {
        $postData = $request->getParsedBody();
        return Connection::get()->query(
            'INSERT INTO lists(user_id, category_id, content) VALUES (?, ?, ?)',
            [$request->auth()->userId(), $postData['category_id'], $postData['content']]
        )
            ->then(function (Result $result) use (&$postData) {
                $postData['id'] = $result->insertId;
                return $this->response->jsonSuccess($postData);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->jsonError($throwable);
            });
    }

    public function update(Request $request, array $params): PromiseInterface
    {
        $postData = $request->getParsedBody();
        $dbParams = [
            $postData['content'],
            carbon()->toString(),
            $params['id'],
            $request->auth()->userId()
        ];
        return Connection::get()->query(
            'UPDATE lists SET content = ?, updated_at = ? WHERE id = ? AND user_id = ?',
            $dbParams
        )
            ->then(function (Result $result) {
                return $this->response->jsonSuccess($result->rows);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->jsonError($throwable);
            });
    }

    public function move(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query(
            'UPDATE lists SET category_id = ?, updated_at = ? WHERE id = ? AND user_id = ?;',
            [$params['catId'], time(), $params['noteId'], $request->auth()->userId()]
        )->then(function () {
            return $this->response->jsonSuccess([
                'message' => 'List item moved successfully.'
            ]);
        })->otherwise(function (Throwable $throwable) {
            return $this->response->jsonError('Moving failed');
        });
    }

    public function delete(Request $request, array $params): PromiseInterface
    {
        return Connection::get()->query('DELETE FROM lists WHERE id = ? AND user_id = ?', [$params['id'], $request->auth()->userId()])
            ->then(function (Result $result) {
                return $this->response->jsonSuccess([]);
            })
            ->otherwise(function (Throwable $throwable) {
                return $this->response->json($throwable);
            });
    }
}