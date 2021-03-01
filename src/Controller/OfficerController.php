<?php

namespace App\Controller;

use App\Service\Governor\GovernorManagementService;
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
    private $govManagementService;

    public function __construct(GovernorManagementService $govManagementService)
    {
        $this->govManagementService = $govManagementService;
    }

    /**
     * @Route("/", methods={"GET"}, name="officer_index")
     * @return Response
     */
    public function scribeIndex(): Response
    {
        return $this->render('officer/index.html.twig', [
            'latestOfficerNotes' => $this->govManagementService->getLatestOfficerNotes()
        ]);
    }
}
