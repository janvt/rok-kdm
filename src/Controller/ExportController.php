<?php

namespace App\Controller;

use App\Exception\ExportException;
use App\Form\Export\ExportCommandersType;
use App\Form\Export\ExportGovDataType;
use App\Service\Export\ExportFilter;
use App\Service\Export\ExportService;
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
     * @Route("/", name="export_index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function exportIndex(Request $request): Response
    {
        return $this->render('export/index.html.twig');
    }

    /**
     * @Route("/gov", name="export_gov_data", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function exportGovData(Request $request): Response
    {
        $exportError = null;

        $form = $this->createForm(ExportGovDataType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exportService = $this->exportService;
            $filter = new ExportFilter($form);

            try {
                $response = new StreamedResponse(function() use ($exportService, $filter) {
                    $handle = fopen('php://output', 'w+');
                    $header = false;

                    foreach ($exportService->streamGovDataExport($filter) as $row) {
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

        return $this->render('export/export_gov_data.html.twig', [
            'form' => $form->createView(),
            'exportError' => $exportError
        ]);
    }

    /**
     * @Route("/commanders", name="export_commander_data", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function exportCommanders(Request $request): Response
    {
        $exportError = null;

        $form = $this->createForm(ExportCommandersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $exportService = $this->exportService;
            $filter = new ExportFilter($form);

            try {
                $response = new StreamedResponse(function() use ($exportService, $filter) {
                    $handle = fopen('php://output', 'w+');
                    $header = false;

                    foreach ($exportService->streamCommanderExport($filter) as $row) {
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
                    'attachment; filename="' . $this->exportService->getFileName($filter, 'commanders') . '.csv"'
                );

                return $response;
            } catch(ExportException $e) {
                $exportError = $e->getMessage();
            }
        }

        return $this->render('export/export_commanders.html.twig', [
            'form' => $form->createView(),
            'exportError' => $exportError
        ]);
    }
}
