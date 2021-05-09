<?php

namespace App\Controller;

use App\Entity\Import;
use App\Exception\ImportException;
use App\Exception\NotFoundException;
use App\Form\Scribe\ConfigureImportType;
use App\Form\Scribe\CreateImportType;
use App\Form\Scribe\GoogleSheetImportType;
use App\Service\Governor\Equipment\EquipmentService;
use App\Service\Import\FieldMapping\ImportMapping;
use App\Service\Import\ImportService;
use App\Util\NotFoundResponse;
use Google_Client;
use Google_Service_Sheets;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/import")
 * @IsGranted("ROLE_SCRIBE")
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
     * @param string $importsDir
     * @return Response
     */
    public function createImport(Request $request, string $importsDir): Response
    {
        $form = $this->createForm(CreateImportType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFileName = null;
            try {
                $uploadedFile = $form->get('csvFile')->getData();
                if ($uploadedFile instanceof UploadedFile) {
                    $uploadedFileName = date('YmdHis') . '_' . $uploadedFile->getClientOriginalName();
                    $uploadedFile->move($importsDir, $uploadedFileName);
                }

                $import = $this->importService->createImport(
                    $this->getUser(),
                    $uploadedFileName,
                    $form->get('csvInput')->getData()
                );

                return $this->redirectToRoute('configure_import', ['importId' => $import->getId()]);
            } catch (ImportException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
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

    /**
     * @Route("/equipment_inventory", name="import_equipment_inventory", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function importEquipmentInventory(
        Request $request,
        KernelInterface $appKernel,
        EquipmentService $equipmentService
    ): Response
    {
        $client = new Google_Client();
        $client->setApplicationName('ROK KDM');
        $client->setScopes(Google_Service_Sheets::SPREADSHEETS_READONLY);
        $client->setAuthConfig($appKernel->getProjectDir() . '/google_sheets_credentials.json');
        $client->setAccessType('offline');

        $service = new Google_Service_Sheets($client);

        $spreadsheetId = '127OlRcd_tZbTgxoz2YPELjqBcoWPKiecAHnO9GraysU';
        $range = 'Sheet1!A2:M';
        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $values = $response->getValues();

        $result = $equipmentService->importInventory($values);

        return $this->render('import/equipment_inventory.html.twig', [
            'num' => $result->getRowsImported(),
            'err' => $result->getInvalidRows()
        ]);
    }

    /**
     * @Route("/google_sheet", name="google_sheet_import", methods={"GET", "POST"})
     * @param Request $request
     * @return Response
     */
    public function googleSheetImport(Request $request): Response
    {
        $form = $this->createForm(GoogleSheetImportType::class, [
            'sheetId' => '10y5OlcZOKsR9ZsZRNKkEBretmZtmu1MsrJnyb070O_s',
            'config' => json_encode($this->getSheetConfig())
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->importService->googleSheetImport(
                    $form->get('sheetId')->getData(),
                    \json_decode($form->get('config')->getData())
                );
            } catch (ImportException $e) {
                $form->addError(new FormError($e->getMessage()));
            }
        }

        return $this->render('import/google_sheet_import.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    private function getSheetConfig(): array
    {
        return [
            'Master' => [
                'id' => 'Player ID'
            ]
        ];
    }
}
