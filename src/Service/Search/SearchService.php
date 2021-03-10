<?php


namespace App\Service\Search;


use App\Entity\User;
use App\Exception\SearchException;
use App\Repository\CommanderRepository;
use App\Repository\GovernorRepository;
use App\Service\Governor\CommanderNames;
use App\Service\Governor\GovernorDetails;
use App\Service\Governor\GovernorDetailsService;
use Symfony\Component\Security\Core\User\UserInterface;

class SearchService
{
    private $detailsService;
    private $govRepo;
    private $commanderRepo;

    const MINIMUM_SEARCH_TERM_LENGTH = 2;

    public function __construct(
        GovernorDetailsService $govDetailsService,
        GovernorRepository $govRepo,
        CommanderRepository $commanderRepo
    )
    {
        $this->detailsService = $govDetailsService;
        $this->govRepo = $govRepo;
        $this->commanderRepo = $commanderRepo;
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
            $searchResult->governors[] = $this->detailsService->createGovernorDetails($gov, true, $user);
        }

        return $searchResult;
    }

    /**
     * @param string $commander1
     * @param string $commander2
     * @return GovernorDetails[]
     * @throws SearchException
     */
    public function searchCommanders(string $commander1, string $commander2): array
    {
        if ($commander1 === $commander2) {
            $commander2 = null;
        }

        if (!$commander1) {
            throw new SearchException('Missing commander');
        }

        if (!\array_key_exists($commander1, CommanderNames::ALL)) {
            throw new SearchException('Invalid commander 1!');
        }

        if ($commander2 && !\array_key_exists($commander2, CommanderNames::ALL)) {
            throw new SearchException('Invalid commander 2!');
        }

        $govs = $this->govRepo->searchCommanders($commander1, $commander2);

        $searchResult = [];
        foreach ($govs as $gov) {
            $searchResult[] = [
                'gov' => $this->detailsService->createGovernorDetails($gov, false)
            ];
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