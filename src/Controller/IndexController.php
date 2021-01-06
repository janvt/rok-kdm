<?php

namespace App\Controller;

use App\Entity\Role;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/")
 */
class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        if ($this->isGranted(Role::ROLE_LUGAL_MEMBER)) {
            return $this->indexLugalMember();
        }

        if ($this->isGranted(Role::ROLE_USER)) {
            return $this->indexAnonymous();
        }

        return $this->render('index.html.twig');
    }

    public function indexLugalMember(): Response
    {
        return $this->render('indexLugalMember.html.twig', []);
    }

    public function indexAnonymous(): Response
    {
        return $this->render('indexAnonymous.html.twig', []);
    }
}
