<?php

namespace App\Controller;

use App\Exception\ExportException;
use App\Form\Export\ExportType;
use App\Form\Export\ExportSnapshotType;
use App\Service\Export\ExportFilter;
use App\Service\Export\ExportService;
use App\Service\Import\ImportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/export")
 * @IsGranted("ROLE_SCRIBE")
 */
class ExportController extends AbstractController
{
    private $exportService;

    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;
    }

    /**
     * @Route("/", name="export_index", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function exportIndex(Request $request): Response
    {
        $exportError = null;

        $form = $this->createForm(ExportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exportService = $this->exportService;
            $filter = new ExportFilter($form);

            try {
                $response = new StreamedResponse(function() use ($exportService, $filter) {
                    $handle = fopen('php://output', 'w+');
                    $header = false;

                    foreach ($exportService->streamFullExport($filter) as $row) {
                        if (!$header) {
                            fputcsv($handle, array_keys($row), ',');
                            $header = true;
                        }

                        fputcsv($handle, $row, ',');
                    }

                    fclose($handle);
                });

                $response->headers->set('Content-Type', 'text/csv; charset=utf-8');
                $response->headers->set(
                    'Content-Disposition',
                    'attachment; filename="' . $this->exportService->getFileName($filter) . '.csv"'
                );

                return $response;
            } catch(ExportException $e) {
                $exportError = $e->getMessage();
            }
        }

        return $this->render('export/index.html.twig', [
            'form' => $form->createView(),
            'exportError' => $exportError
        ]);
    }
}
