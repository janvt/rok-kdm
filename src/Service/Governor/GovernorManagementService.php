<?php


namespace App\Service\Governor;


use App\Entity\Governor;
use App\Entity\GovernorProfileClaim;
use App\Entity\Image;
use App\Entity\OfficerNote;
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

    /**
     * @param Governor $gov
     * @return Governor
     */
    public function save(Governor $gov): Governor
    {
        return $this->govRepo->save($gov);
    }

    /**
     * @param Governor $gov
     * @param OfficerNote $note
     * @param User|UserInterface $user
     * @return OfficerNote
     */
    public function addOfficerNote(Governor $gov, OfficerNote $note, User $user): OfficerNote
    {
        $note->setUser($user);
        $note->setGovernor($gov);
        $note->setCreated(new \DateTime());

        return $this->noteRepo->save($note);
    }

    /**
     * @param int $noteId
     * @return OfficerNote
     * @throws NotFoundException
     */
    public function findOfficerNote(int $noteId): OfficerNote
    {
        $note = $this->noteRepo->find($noteId);
        if (!$note) {
            throw new NotFoundException('note', $noteId);
        }

        return $note;
    }

    public function saveOfficerNote(OfficerNote $note): OfficerNote
    {
        return $this->noteRepo->save($note);
    }

    public function removeOfficerNote(OfficerNote $note)
    {
        $this->noteRepo->remove($note);
    }

    /**
     * @param User|UserInterface $user
     * @return GovernorProfileClaim|null
     */
    public function getPendingProfileClaim(User $user): ?GovernorProfileClaim
    {
        return $this->govProfileClaimRepo->findOneBy([
            'user' => $user,
            'status' => [GovernorProfileClaim::STATUS_OPEN, GovernorProfileClaim::STATUS_MISSING_GOV]
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
     * @param string $status
     * @param int $limit
     * @return GovernorProfileClaim[]
     */
    public function findProfileClaimsByStatus(string $status, int $limit = 100): array
    {
        return $this->govProfileClaimRepo->findBy(
            [
                'status' => $status,
            ],
            [
                'created' => 'DESC'
            ],
            $limit
        );
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
     * @param GovernorProfileClaim $claim
     * @param $govId
     * @return Governor
     * @throws NotFoundException
     */
    public function linkToUser(GovernorProfileClaim $claim, $govId): Governor
    {
        $gov = $this->findGov($govId);
        $gov->setUser($claim->getUser());

        $gov = $this->govRepo->save($gov);

        $claim->setStatus(GovernorProfileClaim::STATUS_VERIFIED);
        $this->govProfileClaimRepo->save($claim);

        return $gov;
    }

    /**
     * @return OfficerNote[]
     */
    public function getLatestOfficerNotes(): array
    {
        return $this->noteRepo->findBy([], ['created' => 'DESC'], 10);
    }

    /**
     * @param int $claimId
     * @param string $status
     * @return GovernorProfileClaim
     * @throws NotFoundException
     */
    public function setClaimStatus(int $claimId, string $status): GovernorProfileClaim
    {
        return $this->govProfileClaimRepo->save(
            $this->findClaim($claimId)->setStatus($status)
        );
    }

    /**
     * @param int $claimId
     * @return GovernorProfileClaim
     * @throws NotFoundException
     */
    public function findClaim(int $claimId): GovernorProfileClaim
    {
        $claim = $this->govProfileClaimRepo->find($claimId);
        if (!$claim) {
            throw new NotFoundException('claim', $claimId);
        }

        return $claim;
    }
}