<?php

namespace App\Controller;

use App\Entity\History;
use App\Entity\Sector;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HistoryController extends AbstractController
{
    /**
     * @Route("/history", name="history")
     */
    public function index()
    {
    
        $historyRepository = $this->getDoctrine()->getRepository(History::class);
        $histories = $historyRepository->findAll();

        $sectorRepository = $this->getDoctrine()->getRepository(Sector::class);
        $sectors = $sectorRepository->findAll();

        return $this->render('history/index.html.twig', [
            'sectors' => $sectors,
            'histories' => $histories,
        ]);

    }

}
