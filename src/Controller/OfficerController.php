<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/officer")
 * @IsGranted("ROLE_OFFICER")
 */
class OfficerController extends AbstractController
{
    public function __construct()
    {
    }

    /**
     * @Route("/", methods={"GET"}, name="officer_index")
     * @return Response
     */
    public function scribeIndex(): Response
    {
        return $this->render('officer/index.html.twig', [
        ]);
    }
}
