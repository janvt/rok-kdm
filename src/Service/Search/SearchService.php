<?php


namespace App\Service\Search;


use App\Exception\SearchException;
use App\Repository\GovernorRepository;
use App\Service\Governor\GovernorDetailsService;

class SearchService
{
    private $govRepo;
    private $detailsService;

    const MINIMUM_SEARCH_TERM_LENGTH = 2;

    public function __construct(GovernorDetailsService $govDetailsService, GovernorRepository $govRepo)
    {
        $this->govRepo = $govRepo;
        $this->detailsService = $govDetailsService;
    }

    /**
     * @param string $searchTerm
     * @return SearchResult
     * @throws SearchException
     */
    public function search(string $searchTerm): SearchResult
    {
        if (\mb_strlen($searchTerm) < self::MINIMUM_SEARCH_TERM_LENGTH) {
            throw new SearchException();
        }

        $searchResult = new SearchResult();

        $govs = $this->govRepo->search($searchTerm);
        foreach ($govs as $gov) {
            $searchResult->governors[] = $this->detailsService->createGovernorDetails($gov);
        }

        return $searchResult;
    }
}