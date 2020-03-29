<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class MainController extends AbstractController
{


    /**
     * @Route("/", name="main")
     */
    public function index()
    {
        $repositoryUsers = $this->getDoctrine()->getRepository(User::class);
        $users = $repositoryUsers->findAll();

        $user = $this->getUser();
        if (isset($user)) {
            foreach ($this->getUser()->getRoles() as $roles) {
                if ($roles == 'ROLE_ADMIN') {
                    return $this->redirectToRoute('easyadmin');
                }
            }
        }


        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $this->getUser(),
            'users' => $users
        ]);
    }
    
    /**
     * @Route("/incidence", name="incidence")
     */
    public function incidene()
    {
        $repositoryUsers = $this->getDoctrine()->getRepository(User::class);
        $users = $repositoryUsers->findAll();

        $user = $this->getUser();
        if (isset($user)) {
            foreach ($this->getUser()->getRoles() as $roles) {
                if ($roles == 'ROLE_ADMIN') {
                    return $this->redirectToRoute('easyadmin');
                }
            }
        }


        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'user' => $this->getUser(),
            'users' => $users
        ]);
    }
}