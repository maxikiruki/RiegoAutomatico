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
   
    /**
     * @Route("/state_update/{checked}/{sector}/{type}", name="state_update", methods={"GET","POST"})
     */
    public function update($checked, $sector, $type)
    {
        //obtengo el sector
        $stateRepository = $this->getDoctrine()->getRepository(State::class);
        $state = $stateRepository->findOneBySector($sector);
        //obtengo si lo tengo que poner true o false
        $on_off=null;
        if($checked == "0"){
            $on_off=true;
        }else{
            $on_off=false;
        }
        //modifico en funcion del type
        $entityManager = $this->getDoctrine()->getManager();

        if($type == "switchMan" && $on_off == true){
            $state->setOnoff(true);
        }
        if($type == "switchMan" && $on_off == false){
            $state->setOnoff(false);
        }    
        
        if($type == "switchPro" && $on_off == true){
            $state->setProgrammed(true);
        }
        if($type == "switchPro" && $on_off == false){
            $state->setProgrammed(false);
            $sector->setSchedule(null);
        }
        $entityManager->persist($sector);
        $entityManager->persist($state);
        $entityManager->flush();



    }
}
