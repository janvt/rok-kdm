<?php

namespace App\Controller;

use App\Repository\GovernorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/governor")
 */
class GovernorController extends AbstractController
{
    /**
     * @Route("/", name="governor_index", methods={"GET"})
     */
    public function index(GovernorRepository $governorRepository): Response
    {
        return $this->render('governor/index.html.twig', [
            'governors' => $governorRepository->findAll(),
        ]);
    }
}
