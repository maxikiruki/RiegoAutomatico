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
            'user' => $this->getUser(),
            'states' => $states,
        ]);
    }
   
    /**
     * @Route("/update_manual/{state}", name="update_manual", methods={"GET","POST"})
     */
    public function update_manual($state)
    {
        //obtengo el sector, como el state guarda el id del state, tengo que buscar en el repositorio de state por el id

        $stateRepository = $this->getDoctrine()->getRepository(State::class);
        $stateCompleto = $stateRepository->findOneByID($state);

        //modifico 
        $entityManager = $this->getDoctrine()->getManager();

        if($stateCompleto->getOnOff()){
            $stateCompleto->setOnoff(false);
        }else{
            $stateCompleto->setOnoff(true);
        }    
        $entityManager->persist($stateCompleto);
        $entityManager->flush();


        return $this->redirectToRoute('state');
    }

    /**
     * @Route("/update_programmed/{state}", name="update_programmed", methods={"GET","POST"})
     */
    public function update_programmed($state)
    {
        //obtengo el sector, como el state guarda el id del state, tengo que buscar en el repositorio de state por el id
        //luego obtengo el sector de ese estado.
        $stateRepository = $this->getDoctrine()->getRepository(State::class);
        $stateCompleto = $stateRepository->findOneByID($state);
        $sector=$stateCompleto->getSector();

        //modifico 
        $entityManager = $this->getDoctrine()->getManager();

        if($stateCompleto->getProgrammed()){
            $stateCompleto->setProgrammed(false);
            //$sector->setLastSchedule($sector->getSchedule());
            //$sector->setSchedule(null);
            
        }else{
            $stateCompleto->setProgrammed(false);
            /*
            if($sector->getLastSchedule() != NULL){
                $sector->setSchedule($sector->getLastSchedule());
                $stateCompleto->setProgrammed(true);
            }
            */
        }
        $entityManager->persist($sector);
        $entityManager->persist($stateCompleto);
        $entityManager->flush();


        return $this->redirectToRoute('state');
    }
}
