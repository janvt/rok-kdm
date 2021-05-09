<?php

namespace App\Controller;

use App\Entity\FeatureFlag;
use App\Entity\Governor;
use App\Entity\OfficerNote;
use App\Exception\NotFoundException;
use App\Form\Governor\AddGovernorType;
use App\Form\Governor\EditEquipmentType;
use App\Form\Governor\EditGovernorType;
use App\Form\OfficerNote\EditOfficerNoteType;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_KINGDOM_MEMBER")
 */
class EquipmentController extends AbstractGovernorController
{
    /**
     * @Route("/g/{id}/equipment", name="governor_edit_equipment", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function editEquipment(string $id, Request $request): Response
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

        $this->equipmentService->ensureAllEquipment($gov);

        $form = $this->createForm(EditEquipmentType::class, $gov);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->govManagementService->save($form->getData());

            if ($form->get('saveAndReturn')->isClicked()) {
                return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
            }
        }

        return $this->render('governor/edit_equipment.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
