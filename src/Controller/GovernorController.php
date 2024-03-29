<?php

namespace App\Controller;

use App\Entity\FeatureFlag;
use App\Entity\Governor;
use App\Entity\OfficerNote;
use App\Entity\Role;
use App\Exception\NotFoundException;
use App\Form\Governor\AddGovernorType;
use App\Form\Governor\EditCommandersType;
use App\Form\Governor\EditGovernorType;
use App\Form\OfficerNote\EditOfficerNoteType;
use App\Util\NotFoundResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/g")
 * @IsGranted("ROLE_KINGDOM_MEMBER")
 */
class GovernorController extends AbstractGovernorController
{
    /**
     * @Route("/{id}", name="governor", methods={"GET"}, requirements={"id"="\d+"})
     * @param string $id
     * @return Response
     */
    public function index(string $id): Response
    {
        try {
            $gov = $this->govManagementService->findGov($id);
        } catch (NotFoundException $e) {
            return new Response('Not found.', Response::HTTP_NOT_FOUND);
        }

        $commanders = null;
        if ($this->featureFlagService->isActive(FeatureFlag::COMMANDERS)) {
            $commanders = $this->commanderService->getAllForGov($gov);
        }

        $equipmentLoadouts = null;
        if ($this->featureFlagService->isActive(FeatureFlag::EQUIPMENT)) {
            $equipmentLoadouts = $this->equipmentService->getLoadouts($gov);
        }

        return $this->render('governor/index.html.twig', [
            'gov' => $this->detailsService->createGovernorDetails($gov, true, $this->getUser()),
            'commanders' => $commanders,
            'equipmentLoadouts' => $equipmentLoadouts,
            'canEditProfile' => $this->canEditProfile($gov),
            'ffCommanders' => $this->featureFlagService->isActive(FeatureFlag::COMMANDERS),
            'ffEquipment' => $this->featureFlagService->isActive(FeatureFlag::EQUIPMENT)
        ]);
    }

    /**
     * @Route("/{id}/commanders", name="governor_edit_commanders", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function editCommanders(string $id, Request $request): Response
    {
        if (!$this->featureFlagService->isActive(FeatureFlag::COMMANDERS)) {
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

        $this->commanderService->ensureAllCommanders($gov);

        $form = $this->createForm(EditCommandersType::class, $gov);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->govManagementService->save($form->getData());

            if ($form->get('saveAndReturn')->isClicked()) {
                return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
            }
        }

        return $this->render('governor/edit_commanders.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/{id}/note", name="governor_add_note", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_OFFICER")
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function addOfficerNote(string $id, Request $request): Response
    {
        try {
            $gov = $this->govManagementService->findGov($id);
        } catch (NotFoundException $e) {
            return new Response('Not found.', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(EditOfficerNoteType::class, new OfficerNote());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $this->govManagementService->addOfficerNote($gov, $form->getData(), $user);

            return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
        }

        return $this->render('governor/edit_note.html.twig' , [
            'form' => $form->createView(),
            'gov' => $gov,
        ]);
    }

    /**
     * @Route("/{id}/note/{noteId}", name="governor_edit_note", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_OFFICER")
     * @param string $id
     * @param int $noteId
     * @param Request $request
     * @return Response
     */
    public function editOfficerNote(string $id, int $noteId, Request $request): Response
    {
        try {
            $gov = $this->govManagementService->findGov($id);
            $note = $this->govManagementService->findOfficerNote($noteId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        $form = $this->createForm(EditOfficerNoteType::class, $note);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->govManagementService->saveOfficerNote($form->getData());

            return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
        }

        return $this->render('governor/edit_note.html.twig' , [
            'form' => $form->createView(),
            'gov' => $gov,
        ]);
    }

    /**
     * @Route("/{id}/note/{noteId}/delete", name="governor_delete_note", methods={"GET"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_OFFICER")
     * @param string $id
     * @param int $noteId
     * @param Request $request
     * @return Response
     */
    public function deleteOfficerNote(string $id, int $noteId, Request $request): Response
    {
        try {
            $gov = $this->govManagementService->findGov($id);
            $note = $this->govManagementService->findOfficerNote($noteId);
        } catch (NotFoundException $e) {
            return new NotFoundResponse($e);
        }

        $this->govManagementService->removeOfficerNote($note);

        return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
    }

    /**
     * @Route("/add", name="governor_add", methods={"GET", "POST"})
     * @IsGranted("ROLE_OFFICER")
     * @param Request $request
     * @return Response
     */
    public function add(Request $request): Response
    {
        $form = $this->createForm(AddGovernorType::class, new Governor());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $gov = null;
            try {
                $gov = $this->govManagementService->findGov($form->get('governorId')->getData());
            } catch (NotFoundException $e) {
                // do nothing
            }

            if ($gov) {
                $form->addError(new FormError('Governor with this id already exists!'));
            } else {
                $gov = $this->govManagementService->save($form->getData());
                return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
            }
        }

        return $this->render('governor/edit.html.twig' , [
            'form' => $form->createView(),
            'gov' => null,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="governor_edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @IsGranted("ROLE_OFFICER")
     * @param string $id
     * @param Request $request
     * @return Response
     */
    public function edit(string $id, Request $request): Response
    {
        try {
            $gov = $this->govManagementService->findGov($id);
        } catch (NotFoundException $e) {
            return new Response('Not found.', Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(EditGovernorType::class, $gov);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->govManagementService->save($gov);

            return $this->redirectToRoute('governor', ['id' => $gov->getGovernorId()]);
        }

        return $this->render('governor/edit.html.twig' , [
            'form' => $form->createView(),
            'gov' => $this->detailsService->createGovernorDetails($gov, true, $this->getUser()),
        ]);
    }
}
