<?php

namespace App\Scrapper;

use QL\Dom\Elements;
use ReactphpQuerylist\Queryable;

class Webpage
{
    public static function extractMetaData(Queryable $queryable): array
    {
        $metaTags = [];

        $queryable->queryList()
            ->find('meta')
            ->each(function (Elements $element) use (&$metaTags) {
                if (!empty($element->attr('name'))) {
                    $metaTags[$element->attr('name')] = $element->attr('content');
                }

                if (!empty($element->attr('property'))) {
                    $metaTags[$element->attr('property')] = $element->attr('content');
                }
            });

        return $metaTags;
    }
}