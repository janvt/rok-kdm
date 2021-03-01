<?php

namespace App\Controller;

use App\Entity\Snapshot;
use App\Exception\NotFoundException;
use App\Exception\SnapshotDataException;
use App\Form\Scribe\CreateSnapshotType;
use App\Form\Scribe\EditGovernorSnapshotType;
use App\Service\Snapshot\SnapshotService;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
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
     * @Route("/snapshot/create", methods={"GET", "POST"}, name="scribe_snapshot_create")
     * @param Request $request
     * @return Response
     * @IsGranted("ROLE_SCRIBE_ADMIN")
     */
    public function scribeSnapshotCreate(Request $request): Response
    {
        $snapshot = new Snapshot();
        $snapshot->setCreated(new \DateTime);
        $form = $this->createForm(CreateSnapshotType::class, $snapshot);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->snapshotService->createSnapshot($snapshot);

                return $this->redirectToRoute('scribe_snapshot_detail', ['snapshotUid' => $snapshot->getUid()]);
            } catch(SnapshotDataException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('scribe/create_snapshot.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/snapshot/{snapshotUid}/populate", methods={"GET"}, name="scribe_snapshot_populate")
     * @param string $snapshotUid
     * @return Response
     * @IsGranted("ROLE_SCRIBE_ADMIN")
     */
    public function scribeSnapshotPopulate(string $snapshotUid): Response
    {
        try {
            $snapshot = $this->snapshotService->getSnapshotForUid($snapshotUid);
            $this->snapshotService->populateSnapshot($snapshot);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('scribe_snapshot_detail', ['snapshotUid' => $snapshot->getUid()]);
    }

    /**
     * @Route("/snapshot/{snapshotUid}/mark_completed", methods={"GET"}, name="scribe_snapshot_mark_completed")
     * @param string $snapshotUid
     * @return Response
     * @IsGranted("ROLE_SCRIBE_ADMIN")
     */
    public function scribeSnapshotMarkCompleted(string $snapshotUid): Response
    {
        try {
            $snapshot = $this->snapshotService->getSnapshotForUid($snapshotUid);
            $this->snapshotService->markCompleted($snapshot);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->redirectToRoute('scribe_snapshot_detail', ['snapshotUid' => $snapshot->getUid()]);
    }

    /**
     * @Route("/snapshot/{snapshotUid}", methods={"GET"}, name="scribe_snapshot_detail")
     * @param string $snapshotUid
     * @return Response
     */
    public function scribeSnapshotDetail(string $snapshotUid, Request $request): Response
    {
        try {
            $snapshot = $this->snapshotService->getSnapshotForUid($snapshotUid);
            $snapshotInfo = $this->snapshotService->createSnapshotInfo($snapshot, $request->get('alliance'));
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->render('scribe/detail.html.twig', [
            'snapshotInfo' => $snapshotInfo,
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
            $markComplete = $form->get('saveAndMarkCompleted')->isClicked();
            $saveAndReturn = $form->get('saveAndReturn')->isClicked();

            if ($markComplete) {
                $govSnapshot->setCompleted(new \DateTime);
            }

            $this->snapshotService->updateGovSnapshot($govSnapshot);

            if ($markComplete || $saveAndReturn) {
                if ($request->get('snapshot')) {
                    return $this->redirectToRoute(
                        'scribe_snapshot_detail',
                        ['snapshotUid' => $request->get('snapshot')]
                    );
                }

                return $this->redirectToRoute('scribe_index');
            }
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
