<?php


namespace App\Controller;


use App\Entity\GovernorProfileClaim;
use App\Entity\Image;
use App\Exception\ImageUploadException;
use App\Exception\NotFoundException;
use App\Exception\SearchException;
use App\Form\Governor\GovernorClaimType;
use App\Service\Governor\GovernorDetailsService;
use App\Service\Governor\GovernorManagementService;
use App\Service\Image\ImageService;
use App\Service\Search\SearchService;
use App\Service\User\UserService;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
    private $imageService;

    public function __construct(
        GovernorManagementService $governorManagementService,
        GovernorDetailsService $detailsService,
        SearchService $searchService,
        UserService $userService,
        ImageService $imageService
    )
    {
        $this->govManagementService = $governorManagementService;
        $this->detailsService = $detailsService;
        $this->searchService = $searchService;
        $this->userService = $userService;
        $this->imageService = $imageService;
    }

    /**
     * @Route("/create", name="governor_claim_create")
     * @param Request $request
     * @return Response
     */
    public function createClaim(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $imageUploadError = null;
        $imageUploadForm = $this->createForm(GovernorClaimType::class);
        $imageUploadForm->handleRequest($request);
        $profileClaim = $this->govManagementService->getPendingProfileClaim($user);
        $profileClaimImage = null;

        if (!$profileClaim) {
            if ($imageUploadForm->isSubmitted() && $imageUploadForm->isValid()) {
                /** @var UploadedFile $uploadedImage */
                if ($uploadedImage = $imageUploadForm['image']->getData()) {
                    try {
                        $image = $this->imageService->handleImageUpload(
                            $uploadedImage,
                            $user,
                            Image::TYPE_PROFILE_CLAIM_PROOF
                        );

                        $profileClaim = $this->govManagementService->addProfileClaim($image, $user);
                    } catch (ImageUploadException $e) {
                        $imageUploadError = 'Could not upload image!';
                    }
                }
            }
        }

        if ($profileClaim) {
            $profileClaimImage = $this->govManagementService->resolveProof($profileClaim);
        }

        return $this->render('governor/create_profile_claim.html.twig', [
            'imageUploadForm' => $imageUploadForm->createView(),
            'imageUploadError' => $imageUploadError,
            'profileClaim' => $profileClaim,
            'profileClaimImage' => $profileClaimImage,
        ]);
    }

    /**
     * @Route("/", name="governor_claims")
     * @IsGranted("ROLE_OFFICER")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('officer/governor_profile_claims.html.twig', [
            'openClaims' => $this->govManagementService->findProfileClaimsByStatus(
                GovernorProfileClaim::STATUS_OPEN
            ),
            'missingGovClaims' => $this->govManagementService->findProfileClaimsByStatus(
                GovernorProfileClaim::STATUS_MISSING_GOV
            ),
            'recentClaims' => $this->govManagementService->findProfileClaimsByStatus(
                GovernorProfileClaim::STATUS_VERIFIED
            )
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

        $searchTerm = trim($request->get('search'));
        $searchResult = [];
        if ($searchTerm) {
            try {
                $searchResult = $this->searchService->search($searchTerm, $this->getUser());
            } catch (SearchException $e) {
                return new Response(null, Response::HTTP_BAD_REQUEST);
            }
        }

        return $this->render('officer/governor_profile_claim.html.twig', [
            'claim' => $claim,
            'profileClaimImage' => $this->govManagementService->resolveProof($claim),
            'searchTerm' => $searchTerm,
            'searchResult' => $searchResult,
        ]);
    }

    /**
     * @Route("/{id}/link/{govId}", name="link_gov_to_user")
     * @IsGranted("ROLE_OFFICER")
     * @param $id
     * @param $govId
     * @return Response
     */
    public function linkToUser($id, $govId): Response
    {
        try {
            $claim = $this->govManagementService->findClaim($id);

            $this->userService->makeKingdomMember($claim->getUser());

            $this->govManagementService->linkToUser($claim, $govId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('governor_claims');
    }

    /**
     * @Route("/{id}/mark_gov_missing", name="claim_mark_gov_missing")
     * @IsGranted("ROLE_OFFICER")
     * @param $id
     * @return Response
     */
    public function markMissing($id): Response
    {
        try {
            $this->govManagementService->setClaimStatus($id, GovernorProfileClaim::STATUS_MISSING_GOV);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('governor_claims');
    }

    /**
     * @Route("/{id}/close", name="close_claim")
     * @IsGranted("ROLE_OFFICER")
     * @param $id
     * @return Response
     */
    public function closeClaim($id): Response
    {
        try {
            $this->govManagementService->setClaimStatus($id, GovernorProfileClaim::STATUS_CLOSED);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('governor_claims');
    }
}