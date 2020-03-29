<?php

namespace App\Controller;

use App\Entity\Sector;
use App\Entity\State;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class StateController extends AbstractController
{
     /**
     * @Route("/state", name="state")
     */
    public function index()
    {
        $stateRepository = $this->getDoctrine()->getRepository(State::class);
        $states = $stateRepository->findAll();

        $sectorRepository = $this->getDoctrine()->getRepository(Sector::class);
        $sectors = $sectorRepository->findAll();


        return $this->render('state/index.html.twig', [
            'sectors' => $sectors,
            'states' => $states,
        ]);
    }
}
