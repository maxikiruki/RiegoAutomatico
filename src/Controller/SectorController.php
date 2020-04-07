<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\Sector;
use App\Entity\State;
use App\Form\SectorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SectorController extends AbstractController
{
     /**
     * @Route("/sector", name="sector")
     */
    public function index()
    {
        $sectorRepository = $this->getDoctrine()->getRepository(Sector::class);
        $sectors = $sectorRepository->findAll();

        $scheduleRepository = $this->getDoctrine()->getRepository(Schedule::class);
        $schedules = $scheduleRepository->findAll();


        return $this->render('sector/index.html.twig', [

            'sectors' => $sectors,
            'schedules' => $schedules,
            'user' => $this->getUser(),
        ]);
    }



    /**
     * @Route("/{id}/edit", name="sector_edit", methods={"GET","POST"})
     */
    public function edit(Request $request,  $id): Response
    {
        $sectorRepository = $this->getDoctrine()->getRepository(Sector::class);
        $sector = $sectorRepository->find($id);
        
        $form = $this->createForm(SectorType::class, $sector);
        $form->handleRequest($request);

        $scheduleRepository = $this->getDoctrine()->getRepository(Schedule::class);
        $schedulesVisible = $scheduleRepository->findVisible();

        if ($form->isSubmitted() && $form->isValid()) {
            
            //Buscar en el repositorio de los estados donde el sector = $sector y ahí setProgrammed false
            $stateRepository = $this->getDoctrine()->getRepository(State::class);
            $state = $stateRepository->findOneBySector($sector);
            if($sector->getSchedule() == null){
                $state->setProgrammed(false);
            }elseif ($sector->getSchedule() != null){
                $state->setProgrammed(true);
            }

            //GUARDAR CAMBIOS
            // $entityManager = $this->getDoctrine()->getManager();
            
            // $entityManager->persist($state);
            // $entityManager->flush();
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('sector');
        }

        return $this->render('sector/edit.html.twig', [
            'sector' => $sector,
            'schedulesVisible' => $schedulesVisible,
            'form' => $form->createView(),
        ]);
    }






}
