<?php


namespace App\Service\Search;


use App\Entity\User;
use App\Exception\SearchException;
use App\Repository\GovernorRepository;
use App\Service\Governor\GovernorDetailsService;
use Symfony\Component\Security\Core\User\UserInterface;

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
     * @param User|UserInterface $user
     * @return SearchResult
     * @throws SearchException
     */
    public function search(string $searchTerm, User $user): SearchResult
    {
        $this->validateSearchTerm($searchTerm);

        $searchResult = new SearchResult();

        $govs = $this->govRepo->search($searchTerm);
        foreach ($govs as $gov) {
            $searchResult->governors[] = $this->detailsService->createGovernorDetails($gov, $user);
        }

        return $searchResult;
    }

    /**
     * @param string $searchTerm
     * @throws SearchException
     */
    private function validateSearchTerm(string $searchTerm)
    {
        if (\mb_strlen($searchTerm) < self::MINIMUM_SEARCH_TERM_LENGTH) {
            throw new SearchException();
        }
    }
}