<?php


namespace App\Service\Search;


use App\Repository\GovernorRepository;
use App\Service\Governor\GovernorDetailsService;

class SearchService
{
    private $govRepo;
    private $detailsService;

    public function __construct(GovernorDetailsService $govDetailsService, GovernorRepository $govRepo)
    {
        $this->govRepo = $govRepo;
        $this->detailsService = $govDetailsService;
    }

    public function search(string $searchTerm): SearchResult
    {
        $searchResult = new SearchResult();

        $govs = $this->govRepo->search($searchTerm);
        foreach ($govs as $gov) {
            $searchResult->governors[] = $this->detailsService->createGovernorDetails($gov);
        }

        return $searchResult;
    }
}