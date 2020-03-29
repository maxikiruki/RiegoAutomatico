<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Entity\Sector;
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
        ]);
    }



    /**
     * @Route("/{id}/edit", name="sector_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Sector $sector): Response
    {
        $form = $this->createForm(SectorType::class, $sector);
        $form->handleRequest($request);

        $scheduleRepository = $this->getDoctrine()->getRepository(Schedule::class);
        $schedulesVisible = $scheduleRepository->findVisible();

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('sector_index');
        }

        return $this->render('sector/edit.html.twig', [
            'sector' => $sector,
            'scheduleVisible' => $schedulesVisible,
            'form' => $form->createView(),
        ]);
    }






}
