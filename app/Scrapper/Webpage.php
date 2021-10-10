<?php

namespace App\Scrapper;

use QL\Dom\Elements;
use ReactphpQuerylist\Queryable;

class Webpage
{
    protected static array $possiblyUrlMetaTags = [
        'og:url',
        'og:image',
        'og:audio',
        'og:video',
    ];

    public static function mayHoldUrl(string $attrName): bool
    {
        return in_array($attrName, self::$possiblyUrlMetaTags);
    }


    public static function extractMetaData(Queryable $queryable): array
    {
        $metaTags = [];

        $queryable->queryList()
            ->find('meta')
            ->each(function (Elements $element) use (&$metaTags) {
                $attrName = $element->attr('name');
                $attrProperty = $element->attr('property');

                if (!empty($attrName)) {
                    $metaTags[$attrName] = self::mayHoldUrl($attrName)
                        ? self::readyUrl($element->attr('content'))
                        : $element->attr('content');
                }

                if (!empty($attrProperty)) {
                    $metaTags[$attrProperty] = self::mayHoldUrl($attrProperty)
                        ? self::readyUrl($element->attr('content'))
                        : $element->attr('content');
                }
            });

        $metaTags['displayable'][] = self::readyUrl(self::findDisplayableImage($queryable) ?? '');

        return $metaTags;
    }

    public static function findDisplayableImage(Queryable $queryable): ?string
    {
        return $queryable->queryList()
            ->find('link[as="image"]')
            ->eq(0)
            ->attr('href');
    }

    protected static function readyUrl(string $url): string
    {
        if (empty($url)) return $url;

        if ('//' == substr($url, 0, 2)) {
            $url = substr($url, 2);
        }

        if (
            false == strstr($url, 'http://')
            && false == strstr($url, 'https://')
            && false == strstr($url, 'www.')
        ) {
            $url = 'http://' . $url;
        }

        return $url;
    }
}