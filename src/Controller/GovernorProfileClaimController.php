<?php


namespace App\Controller;


use App\Exception\NotFoundException;
use App\Exception\SearchException;
use App\Service\Governor\GovernorDetailsService;
use App\Service\Governor\GovernorManagementService;
use App\Service\Search\SearchService;
use App\Service\User\UserService;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/claims")
 */
class GovernorProfileClaimController extends AbstractController
{
    private $govManagementService;
    private $detailsService;
    private $searchService;
    private $userService;

    public function __construct(
        GovernorManagementService $governorManagementService,
        GovernorDetailsService $detailsService,
        SearchService $searchService,
        UserService $userService
    )
    {
        $this->govManagementService = $governorManagementService;
        $this->detailsService = $detailsService;
        $this->searchService = $searchService;
        $this->userService = $userService;
    }

    /**
     * @Route("/", name="governor_claims")
     * @IsGranted("ROLE_OFFICER")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('officer/governorProfileClaims.html.twig', [
            'claims' => $this->govManagementService->getOpenProfileClaims()
        ]);
    }

    /**
     * @Route("/make_kingdom_member/{id}", name="make_kingdom_member")
     * @IsGranted("ROLE_OFFICER")
     * @param $id
     * @return Response
     */
    public function makeKingdomMember($id, Request $request)
    {
        try {
            $this->userService->makeKingdomMember($id);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        if ($request->query->has('redirect_to_claim')) {
            return $this->redirectToRoute('governor_claim', [
                'id' => $request->query->get('redirect_to_claim')
            ]);
        }

        return $this->redirectToRoute('governor_claims');
    }

    /**
     * @Route("/{id}", name="governor_claim")
     * @IsGranted("ROLE_OFFICER")
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function claim($id, Request $request): Response
    {
        $claim = $this->govManagementService->getProfileClaim($id);
        if (!$claim) {
            return new Response('Not found', Response::HTTP_NOT_FOUND);
        }

        $searchTerm = $request->get('search');
        $searchResult = [];
        if ($searchTerm) {
            try {
                $searchResult = $this->searchService->search($searchTerm, $this->getUser());
            } catch (SearchException $e) {
                return new Response(null, Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->render('officer/governorProfileClaim.html.twig', [
            'claim' => $claim,
            'profileClaimImage' => $this->govManagementService->resolveProof($claim),
            'searchTerm' => $searchTerm,
            'searchResult' => $searchResult,
        ]);
    }

    /**
     * @Route("{id}/link/{govId}", name="link_gov_to_user")
     * @IsGranted("ROLE_OFFICER")
     * @param $id
     * @param $govId
     * @return Response
     */
    public function linkToUser($id, $govId): Response
    {
        try {
            $this->govManagementService->linkToUser($id, $govId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('governor_claims');
    }
}