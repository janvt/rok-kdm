<?php

namespace App\Controller;

use App\Form\Export\ExportAllType;
use App\Form\Export\ExportSnapshotType;
use App\Service\Import\ImportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/export")
 * @IsGranted("ROLE_SCRIBE")
 */
class ExportController extends AbstractController
{
    private $exportService;

    public function __construct(ImportService $governorImportService)
    {
        $this->exportService = $governorImportService;
    }

    /**
     * @Route("/", name="export_index", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function exportIndex(Request $request): Response
    {
        $formExportAll = $this->createForm(ExportAllType::class);
        $formExportAll->handleRequest($request);

        if ($formExportAll->isSubmitted() && $formExportAll->isValid()) {

        }

        $formExportSnapshot = $this->createForm(ExportSnapshotType::class);
        $formExportSnapshot->handleRequest($request);

        if ($formExportSnapshot->isSubmitted() && $formExportSnapshot->isValid()) {

        }

        return $this->render('export/index.html.twig', [
            'formExportAll' => $formExportAll->createView(),
            'formExportSnapshot' => $formExportSnapshot->createView(),
        ]);
    }
}
