<?php

namespace App\Controller;

use App\Entity\FeatureFlag;
use App\Exception\SearchException;
use App\Form\Search\CommanderSearchType;
use App\Service\FeatureFlag\FeatureFlagService;
use App\Service\Governor\GovernorManagementService;
use App\Service\Search\SearchService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/officer")
 * @IsGranted("ROLE_OFFICER")
 */
class OfficerController extends AbstractController
{
    private $govManagementService;
    private $featureFlagService;
    private $searchService;

    public function __construct(
        GovernorManagementService $govManagementService,
        FeatureFlagService $featureFlagService,
        SearchService $searchService
    )
    {
        $this->govManagementService = $govManagementService;
        $this->featureFlagService = $featureFlagService;
        $this->searchService = $searchService;
    }

    /**
     * @Route("/", methods={"GET"}, name="officer_index")
     * @return Response
     */
    public function officerIndex(): Response
    {
        return $this->render('officer/index.html.twig', [
            'latestOfficerNotes' => $this->govManagementService->getLatestOfficerNotes(),
            'ffCommanders' => $this->featureFlagService->isActive(FeatureFlag::COMMANDERS)
        ]);
    }

    /**
     * @Route("/search/commanders", methods={"GET"}, name="commander_search")
     * @param Request $request
     * @return Response
     */
    public function searchCommanders(Request $request): Response
    {
        if (!$this->featureFlagService->isActive(FeatureFlag::COMMANDERS)) {
            return $this->redirectToRoute('officer_index');
        }

        $form = $this->createForm(CommanderSearchType::class);
        $form->handleRequest($request);

        $searchResult = null;
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $searchResult = $this->searchService->searchCommanders(
                    $request->query->get('commander1'),
                    $request->query->get('commander2')
                );
            } catch (SearchException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('officer/commander_search.html.twig', [
            'form' => $form->createView(),
            'searchResult' => $searchResult
        ]);
    }
}
