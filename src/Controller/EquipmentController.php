<?php

namespace App\Controller;

use App\Entity\FeatureFlag;
use App\Entity\Governor;
use App\Exception\NotFoundException;
use App\Form\Governor\EditEquipmentType;
use App\Form\Governor\EquipmentLoadoutType;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/g/{id}", requirements={"id"="\d+"})
 * @IsGranted("ROLE_KINGDOM_MEMBER")
 */
class EquipmentController extends AbstractGovernorController
{
    /**
     * @Route("/equipment", name="governor_edit_equipment", methods={"GET"})
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function index(string $id, Request $request): Response
    {
        $gov = $this->pageLoadShit($id);
        if ($gov instanceof Response) {
            return $gov;
        }

        $loadouts = $this->equipmentService->getLoadouts($gov);

        return $this->render('governor/equipment/index.twig', [
            'gov' => $this->detailsService->createGovernorDetails($gov, false, $this->getUser()),
            'equipmentLoadouts' => $loadouts,
            'canEdit' => $this->canEditProfile($gov)
        ]);
    }

    /**
     * @Route("/equipment/loadout/add", name="governor_equipment_loadout_add", methods={"GET"})
     * @param int $id
     * @return Response
     */
    public function addEquipmentLoadout(int $id): Response
    {
        $gov = $this->pageLoadShit($id);
        if ($gov instanceof Response) {
            return $gov;
        }

        $loadout = $this->equipmentService->addLoadout($gov);

        return $this->redirectToRoute('governor_equipment_loadout_edit', [
            'id' => $gov->getGovernorId(),
            'loadoutId' => $loadout->getId()
        ]);
    }

    /**
     * @Route(
     *     "/equipment/loadout/{loadoutId}/edit",
     *     name="governor_equipment_loadout_edit",
     *     methods={"GET", "POST"},
     *     requirements={"loadoutId"="\d+"}
     * )
     * @param int $id
     * @param int $loadoutId
     * @param Request $request
     * @return Response
     */
    public function loadoutEdit(int $id, int $loadoutId, Request $request): Response
    {
        $gov = $this->pageLoadShit($id);
        if ($gov instanceof Response) {
            return $gov;
        }

        try {
            $loadout = $this->equipmentService->getLoadout($loadoutId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        $form = $this->createForm(EquipmentLoadoutType::class, $loadout);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->equipmentService->saveLoadout($form->getData());

            if ($form->get('saveAndReturn')->isClicked()) {
                return $this->redirectToRoute('governor_edit_equipment', ['id' => $gov->getGovernorId()]);
            }
        }

        return $this->render('governor/equipment/edit_set.twig', [
            'loadout' => $loadout,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route(
     *     "/equipment/loadout/{loadoutId}/delete",
     *     name="governor_equipment_loadout_delete",
     *     methods={"GET", "POST"},
     *     requirements={"loadoutId"="\d+"}
     * )
     * @param int $id
     * @param int $loadoutId
     * @param Request $request
     * @return Response
     */
    public function loadoutDelete(int $id, int $loadoutId, Request $request): Response
    {
        $gov = $this->pageLoadShit($id);
        if ($gov instanceof Response) {
            return $gov;
        }

        try {
            $loadout = $this->equipmentService->getLoadout($loadoutId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        $this->equipmentService->deleteLoadout($loadout);

        return $this->redirectToRoute('governor_edit_equipment', ['id' => $gov->getGovernorId()]);
    }

    /**
     * @param int $id
     * @return Response|Governor
     */
    private function pageLoadShit(int $id)
    {
        if (!$this->featureFlagService->isActive(FeatureFlag::EQUIPMENT)) {
            return new Response('Forbidden!', Response::HTTP_FORBIDDEN);
        }

        try {
            $gov = $this->govManagementService->findGov($id);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        if (!$this->canEditProfile($gov)) {
            return new Response('Access Denied!', Response::HTTP_UNAUTHORIZED);
        }

        return $gov;
    }
}
