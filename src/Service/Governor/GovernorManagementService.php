<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorProfileClaim;
use App\Entity\Image;
use App\Entity\OfficerNote;
use App\Entity\Role;
use App\Entity\User;
use App\Exception\GovDataException;
use App\Exception\NotFoundException;
use App\Repository\GovernorProfileClaimRepository;
use App\Repository\GovernorRepository;
use App\Repository\OfficerNoteRepository;
use App\Repository\UserRepository;
use App\Service\Image\ImageService;
use Symfony\Component\Security\Core\User\UserInterface;

class GovernorManagementService
{
    private $imageService;
    private $userRepo;
    private $govRepo;
    private $noteRepo;
    private $govProfileClaimRepo;

    public function __construct(
        ImageService $imageService,
        UserRepository $userRepo,
        GovernorRepository $govRepo,
        OfficerNoteRepository $noteRepo,
        GovernorProfileClaimRepository $govProfileClaimRepo
    )
    {
        $this->imageService = $imageService;
        $this->userRepo = $userRepo;
        $this->govRepo = $govRepo;
        $this->noteRepo = $noteRepo;
        $this->govProfileClaimRepo = $govProfileClaimRepo;
    }

    /**
     * @param $id
     * @return Governor
     * @throws NotFoundException
     */
    public function findGov($id): Governor
    {
        $govs = $this->govRepo->findBy(['governor_id' => $id]);
        if (count($govs) !== 1) {
            throw new NotFoundException('gov', $id);
        }

        return $govs[0];
    }

    public function addOfficerNote(Governor $gov, OfficerNote $note, User $user): OfficerNote
    {
        $note->setUser($user);
        $note->setGovernor($gov);
        $note->setCreated(new \DateTime());

        return $this->noteRepo->save($note);
    }

    /**
     * @param User|UserInterface $user
     * @return GovernorProfileClaim|null
     */
    public function getOpenProfileClaim(User $user): ?GovernorProfileClaim
    {
        return $this->govProfileClaimRepo->findOneBy([
            'user' => $user,
            'status' => GovernorProfileClaim::STATUS_OPEN
        ]);
    }

    /**
     * @param $id
     * @return GovernorProfileClaim|null
     */
    public function getProfileClaim($id): ?GovernorProfileClaim
    {
        return $this->govProfileClaimRepo->find($id);
    }

    /**
     * @return GovernorProfileClaim[]
     */
    public function getOpenProfileClaims(): array
    {
        return $this->govProfileClaimRepo->findBy([
            'status' => GovernorProfileClaim::STATUS_OPEN
        ]);
    }

    /**
     * @param Image $image
     * @param User|UserInterface $user
     * @return GovernorProfileClaim
     */
    public function addProfileClaim(Image $image, User $user): GovernorProfileClaim
    {
        $govProfileClaim = new GovernorProfileClaim();
        $govProfileClaim->setUser($user);
        $govProfileClaim->setProof(GovernorProfileClaim::PROOF_TYPE_IMAGE, $image->getId());

        return $this->govProfileClaimRepo->save($govProfileClaim);
    }

    /**
     * @param GovernorProfileClaim $claim
     * @return string
     * @throws GovDataException
     */
    public function resolveProof(GovernorProfileClaim $claim): string
    {
        if (!$claim->isProofType(GovernorProfileClaim::PROOF_TYPE_IMAGE)) {
            throw new GovDataException('Invalid proof type!');
        }

        return $this->imageService->getPublicPath($claim->getProofId());
    }

    /**
     * @param $claimId
     * @param $govId
     * @return Governor
     * @throws NotFoundException
     */
    public function linkToUser($claimId, $govId): Governor
    {
        $claim = $this->govProfileClaimRepo->find($claimId);
        if (!$claim) {
            throw new NotFoundException('claim', $claimId);
        }

        $gov = $this->findGov($govId);
        $gov->setUser($claim->getUser());

        $gov = $this->govRepo->save($gov);

        $claim->setStatus(GovernorProfileClaim::STATUS_VERIFIED);
        $this->govProfileClaimRepo->save($claim);

        return $gov;
    }
}