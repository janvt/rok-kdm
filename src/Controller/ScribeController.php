<?php

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Form\Scribe\EditGovernorSnapshotType;
use App\Service\Snapshot\SnapshotService;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/scribe")
 * @IsGranted("ROLE_SCRIBE")
 */
class ScribeController extends AbstractController
{
    private $snapshotService;

    public function __construct(SnapshotService $snapshotService)
    {
        $this->snapshotService = $snapshotService;
    }

    /**
     * @Route("/", methods={"GET"}, name="scribe_index")
     * @param SnapshotService $snapshotService
     * @return Response
     */
    public function scribeIndex(): Response
    {
        return $this->render('scribe/index.html.twig', [
            'snapshots' => $this->snapshotService->getSnapshotsInfo()
        ]);
    }

    /**
     * @Route("/snapshot/{snapshotUid}", methods={"GET"}, name="scribe_snapshot_detail")
     * @param string $snapshotUid
     * @return Response
     */
    public function scribeSnapshotDetail(string $snapshotUid): Response
    {
        try {
            $snapshot = $this->snapshotService->getSnapshotForUid($snapshotUid);
            $snapshotInfo = $this->snapshotService->createSnapshotInfo($snapshot);
            $completeSnapshots = $this->snapshotService->getCompleteGovSnapshots($snapshot);
            $incompleteSnapshots = $this->snapshotService->getIncompleteGovSnapshots($snapshot);
            $missingGovs = $this->snapshotService->getMissingGovs($snapshot);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->render('scribe/detail.html.twig', [
            'snapshotInfo' => $snapshotInfo,
            'completeSnapshots' => $completeSnapshots,
            'incompleteSnapshots' => $incompleteSnapshots,
            'missingGovs' => $missingGovs,
        ]);
    }

    /**
     * @Route("/gov-snapshot/{id}/", methods={"GET", "POST"}, name="scribe_gov_snapshot_edit")
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function editGovSnapshot(string $id, Request $request) {
        $snapshot = null;

        try {
            $govSnapshot = $this->snapshotService->getGovSnapshot($id);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        $form = $this->createForm(EditGovernorSnapshotType::class, $govSnapshot);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->snapshotService->updateGovSnapshot($govSnapshot);
        }

        return $this->render('scribe/edit_gov_snapshot.html.twig', [
            'form' => $form->createView(),
            'govSnapshot' => $govSnapshot,
        ]);
    }

    /**
     * @Route("/snapshot/{snapshotUid}/create/{govId}", methods={"GET"}, name="scribe_gov_snapshot_create")
     * @param int $govId
     * @param string $snapshotUid
     * @return Response
     */
    public function createGovSnapshot(int $govId, string $snapshotUid) {
        try {
            $snapshot = $this->snapshotService->getSnapshotForUid($snapshotUid);
            $govSnapshot = $this->snapshotService->createGovSnapshot($snapshot->getId(), $govId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('scribe_gov_snapshot_edit', [
            'id' => $govSnapshot->getId(),
            'snapshot' => $snapshot->getUid()
        ]);
    }
}
