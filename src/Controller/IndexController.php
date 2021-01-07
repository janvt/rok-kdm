<?php

namespace App\Controller;

use App\Entity\Role;
use App\Service\Search\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    private $searchService;

    public function __construct(SearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->isGranted(Role::ROLE_LUGAL_MEMBER)) {
            return $this->indexLugalMember($request);
        }

        if ($this->isGranted(Role::ROLE_USER)) {
            return $this->indexAnonymous();
        }

        return $this->render('index.html.twig');
    }

    public function indexLugalMember(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_LUGAL_MEMBER);

        $searchResult = null;
        $searchTerm = $request->get('search');
        if ($searchTerm) {
            $searchResult = $this->searchService->search($searchTerm);
        }
        dump($searchResult);

        return $this->render('indexLugalMember.html.twig', [
            'searchTerm' => $searchTerm,
            'searchResult' => $searchResult
        ]);
    }

    public function indexAnonymous(): Response
    {
        return $this->render('indexAnonymous.html.twig', []);
    }
}
