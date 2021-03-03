<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Role;
use App\Exception\ImageUploadException;
use App\Exception\SearchException;
use App\Service\Governor\GovernorDetailsService;
use App\Service\Governor\GovernorManagementService;
use App\Service\Image\ImageService;
use App\Service\Search\SearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Image as ImageConstraint;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    private $searchService;
    private $imageService;
    private $govManagementService;
    private $govDetailsService;

    public function __construct(
        SearchService $searchService,
        ImageService $imageService,
        GovernorManagementService $govManagementService,
        GovernorDetailsService $govDetailsService
    )
    {
        $this->searchService = $searchService;
        $this->imageService = $imageService;
        $this->govManagementService = $govManagementService;
        $this->govDetailsService = $govDetailsService;
    }

    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response
    {
        if ($this->isGranted(Role::ROLE_KINGDOM_MEMBER)) {
            return $this->indexKingdomMember($request);
        }

        if ($this->isGranted(Role::ROLE_USER)) {
            return $this->indexAnonymous($request);
        }

        return $this->render('index.html.twig');
    }

    public function indexKingdomMember(Request $request): Response
    {
        $this->denyAccessUnlessGranted(Role::ROLE_KINGDOM_MEMBER);

        $searchResult = null;
        $featuredGovs = null;

        $searchTerm = $request->get('search');
        if ($searchTerm) {
            try {
                $searchResult = $this->searchService->search($searchTerm, $this->getUser());
            } catch (SearchException $e) {
                return new Response(null, Response::HTTP_BAD_REQUEST);
            }
        } else {
            $featuredGovs = $this->govDetailsService->getFeaturedGovs($this->getUser());
        }

        return $this->render('index_member.html.twig', [
            'searchTerm' => $searchTerm,
            'searchResult' => $searchResult,
            'featuredGovs' => $featuredGovs
        ]);
    }

    public function indexAnonymous(Request $request): Response
    {
        $user = $this->getUser();
        $imageUploadForm = null;
        $imageUploadError = false;
        $profileClaimImage = null;

        $profileClaim = $this->govManagementService->getOpenProfileClaim($user);

        if (!$profileClaim) {
            $imageUploadForm = $this->createFormBuilder()
                ->add(
                    'image',
                    FileType::class,
                    [
                        'required' => false,
                        'label' => 'Governor Profile Screenshot',
                        'mapped' => false,
                        'constraints' => [
                            new ImageConstraint(['maxSize' => '10M'])
                        ],
                    ]
                )
                ->add('submit', SubmitType::class)
                ->getForm();

            $imageUploadForm->handleRequest($request);
            if ($imageUploadForm->isSubmitted() && $imageUploadForm->isValid()) {
                /** @var File $uploadedImage */
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

        return $this->render('index_anon.html.twig', [
            'imageUploadForm' => $imageUploadForm ? $imageUploadForm->createView() : null,
            'imageUploadError' => $imageUploadError,
            'profileClaim' => $profileClaim,
            'profileClaimImage' => $profileClaimImage,
        ]);
    }
}
