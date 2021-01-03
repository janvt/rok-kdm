<?php

namespace App\Controller;

use App\Entity\Governor;
use App\Form\GovernorType;
use App\Repository\GovernorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/governor")
 */
class GovernorController extends AbstractController
{
    /**
     * @Route("/", name="governor_index", methods={"GET"})
     */
    public function index(GovernorRepository $governorRepository): Response
    {
        return $this->render('governor/index.html.twig', [
            'governors' => $governorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="governor_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $governor = new Governor();
        $form = $this->createForm(GovernorType::class, $governor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($governor);
            $entityManager->flush();

            return $this->redirectToRoute('governor_index');
        }

        return $this->render('governor/new.html.twig', [
            'governor' => $governor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="governor_show", methods={"GET"})
     */
    public function show(Governor $governor): Response
    {
        return $this->render('governor/show.html.twig', [
            'governor' => $governor,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="governor_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Governor $governor): Response
    {
        $form = $this->createForm(GovernorType::class, $governor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('governor_index');
        }

        return $this->render('governor/edit.html.twig', [
            'governor' => $governor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="governor_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Governor $governor): Response
    {
        if ($this->isCsrfTokenValid('delete'.$governor->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($governor);
            $entityManager->flush();
        }

        return $this->redirectToRoute('governor_index');
    }
}
