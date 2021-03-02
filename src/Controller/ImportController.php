<?php

namespace App\Controller;

use App\Entity\Import;
use App\Exception\ImportException;
use App\Exception\NotFoundException;
use App\Form\Scribe\ConfigureImportType;
use App\Form\Scribe\CreateImportType;
use App\Service\Import\ImportMapping;
use App\Service\Import\ImportService;
use App\Util\NotFoundResponse;
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

    public function __construct(ImportService $governorImportService)
    {
        $this->importService = $governorImportService;
    }

    /**
     * @Route("/create", name="create_import", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function createImport(Request $request): Response
    {
        $form = $this->createForm(CreateImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $import = $this->importService->createImport($form->get('csv')->getData(), $this->getUser());

            return $this->redirectToRoute('configure_import', ['importId' => $import->getId()]);
        }

        return $this->render('import/create_import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{importId}/configure", name="configure_import", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function configureImport($importId, Request $request): Response
    {
        try {
            $import = $this->importService->getImport($importId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        $importPreview = $this->importService->createPreviewForImport($import);

        $form = $this->createForm(ConfigureImportType::class, $importPreview);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $import->setUpdated(new \DateTime);

            if ($form->get('cancel')->isClicked()) {
                $import->setStatus(Import::STATUS_CANCELED);
                $this->importService->save($import);

                return $this->redirectToRoute('scribe_index');
            }

            try {
                $import->setMappings((string) ImportMapping::fromForm($form));
                $import->setSnapshot($form->get('snapshot')->getData());
                $this->importService->save($import);

                if ($form->get('complete')->isClicked()) {
                    $this->importService->completeImport(
                        $import,
                        $form->get('addNewGovernors')->getData()
                    );

                    return $this->redirectToRoute('scribe_index');
                }

                // refresh preview after mappings were updated
                $importPreview = $this->importService->createPreviewForImport($import);
            } catch (ImportException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('import/configure_import.html.twig', [
            'form' => $form->createView(),
            'preview' => $importPreview
        ]);
    }
}
