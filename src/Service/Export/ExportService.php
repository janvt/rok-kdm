<?php


namespace App\Service\Export;


use App\Service\Governor\GovernorDetailsService;

class ExportService
{
    private $govDetailsService;

    public function __construct(
        GovernorDetailsService $govDetailsService
    ) {
        $this->govDetailsService = $govDetailsService;
    }

    public function streamFullExport(?string $alliance)
    {

    }
}