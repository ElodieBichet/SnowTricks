<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigVideoExtension extends AbstractExtension
{
    protected $embedUrlTemplates;

    public function __construct(array $embedUrlTemplates)
    {
        $this->embedUrlTemplates = $embedUrlTemplates;
    }

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
        $embedUrlTemplates = $this->embedUrlTemplates;

        foreach ($embedUrlTemplates as $key => $value) {
            if (stripos($url, $key)) {
                $embedUrl = sprintf($value, $videoId);
            }
        }

        return $embedUrl;
    }
}
