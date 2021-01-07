<?php

namespace App\Controller;

use App\Repository\GovernorRepository;
use App\Service\Governor\GovernorDetailsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/g")
 */
class GovernorController extends AbstractController
{
    /**
     * @Route("/{id}", name="governor", methods={"GET"})
     * @param string $id
     * @param GovernorRepository $governorRepository
     * @param GovernorDetailsService $detailsService
     * @return Response
     */
    public function index(string $id, GovernorRepository $governorRepository, GovernorDetailsService $detailsService): Response
    {
        $govs = $governorRepository->findBy(['governor_id' => $id]);
        if (count($govs) === 0) {
            return new Response('Not found.', Response::HTTP_NOT_FOUND);
        }

        return $this->render('governor/index.html.twig', [
            'gov' => $detailsService->createGovernorDetails($govs[0]),
        ]);
    }
}
