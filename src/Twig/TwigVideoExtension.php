<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigVideoExtension extends AbstractExtension
{

    public function getFilters()
    {
        return [
            new TwigFilter('embedUrl', [$this, 'embedUrlFilter'])
        ];
    }

    public function embedUrlFilter(?string $url): ?string
    {
        $embedUrl = $url;
        $videoId = substr($url, strrpos($url, '/') + 1);

        // if Youtube
        if (stripos($url, 'youtu')) {
            $embedUrl = 'https://www.youtube.com/embed/' . $videoId . '?controls=2';
        }
        // if Dailymotion
        if (stripos($url, 'dai.ly') || stripos($url, 'daily')) {
            $embedUrl = 'https://www.dailymotion.com/embed/video/' . $videoId;
        }
        // if vimeo
        if (stripos($url, 'vimeo')) {
            $embedUrl = 'https://player.vimeo.com/video/' . $videoId;
        }

        return $embedUrl;
    }
}
