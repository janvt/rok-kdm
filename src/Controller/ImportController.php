<?php

namespace App\Controller;

use App\Exception\ImportException;
use App\Form\Scribe\ImportGovernersType;
use App\Service\Governor\GovernorImportService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/import")
 * @IsGranted("ROLE_SCRIBE_ADMIN")
 */
class ImportController extends AbstractController
{
    private $importService;

    public function __construct(GovernorImportService $governorImportService)
    {
        $this->importService = $governorImportService;
    }

    /**
     * @Route("/governors", name="import_governors", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function importGovernors(Request $request): Response
    {
        $form = $this->createForm(ImportGovernersType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->importService->processCSV($form->get('csv')->getData());
            } catch (ImportException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('import/import_governors.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/governors", name="import_governor_snapshots", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function importGovernorSnapshots(Request $request): Response
    {
        return $this->render('import/import_governor_snapshots.html.twig');
    }
}
