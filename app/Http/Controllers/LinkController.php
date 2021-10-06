<?php

namespace App\Http\Controllers;

use App\Scrapper\Webpage;
use Clue\React\SQLite\Result;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use ReactphpQuerylist\Client;
use ReactphpQuerylist\Queryable;
use Server\Http\Request;
use Throwable;

class LinkController extends Controller
{
    /**
     * @param Request $request
     * @return PromiseInterface|Response
     */
    public function meta(Request $request)
    {
        $link = $request->getParsedBody()['link'];

        if (empty($link)) {
            return $this->response->jsonError('Link field must not be empty');
        }

        return database()
            ->query('SELECT * FROM link_previews WHERE href = ?', [$link])
            ->then(function (Result $result) use ($link) {
                if (empty($result->rows)) {
                    return $this->scrapeLink($link);
                }

                return $this->response->jsonSuccess(\Safe\json_decode($result->rows[0]['meta']));
            })
            ->otherwise(function () use ($link) {
                return $this->scrapeLink($link);
            });
    }

    protected function scrapeLink(string $url)
    {
        return Client::get($url)
            ->then(function (Queryable $queryable) use ($url) {
                $metaTags = Webpage::extractMetaData($queryable);

                if (!empty($metaTags)) {
                    database()->query('INSERT INTO link_previews(href, meta) VALUES (?, ?)', [
                        $url, \Safe\json_encode($metaTags)
                    ]);
                }

                return $this->response->jsonSuccess($metaTags);
            })
            ->otherwise(function (Throwable $error) {
                return $this->response->jsonError($error);
            });
    }
}