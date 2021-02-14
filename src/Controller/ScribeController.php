<?php

namespace App\Controller;

use App\Exception\NotFoundException;
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
    /**
     * @Route("/", methods={"GET"}, name="scribe_index")
     * @param SnapshotService $snapshotService
     * @return Response
     */
    public function scribeIndex(
        SnapshotService $snapshotService
    ): Response
    {
        return $this->render('scribe/index.html.twig', [
            'snapshots' => $snapshotService->getSnapshotsInfo()
        ]);
    }

    /**
     * @Route("/snapshot/{snapshotUid}", methods={"GET"}, name="scribe_snapshot_detail")
     * @param string $snapshotUid
     * @param SnapshotService $snapshotService
     * @return Response
     */
    public function scribeSnapshotDetail(
        string $snapshotUid,
        SnapshotService $snapshotService
    ): Response
    {
        try {
            $snapshot = $snapshotService->getSnapshotForUuid($snapshotUid);
            $snapshotInfo = $snapshotService->createSnapshotInfo($snapshot);
            $incompleteSnapshots = $snapshotService->getIncompleteGovSnapshots($snapshot);
            $missingGovs = $snapshotService->getMissingGovs($snapshot);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        return $this->render('scribe/detail.html.twig', [
            'snapshotInfo' => $snapshotInfo,
            'incompleteSnapshots' => $incompleteSnapshots,
            'missingGovs' => $missingGovs,
        ]);
    }
}
