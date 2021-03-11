<?php


namespace App\Util;


use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigCustomNumberFormat extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('custom_nf', [$this, 'formatNumber'], ['is_safe' => ['html']]),
        ];
    }

    public function formatNumber($number, $decimals = 0, $decPoint = '.', $thousandsSep = ','): string
    {
        if (!$number) {
            return '<small class="text-muted">N/A</small>';
        }

        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }
}