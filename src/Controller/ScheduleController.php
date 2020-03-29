<?php

namespace App\Controller;

use App\Entity\Schedule;
use App\Form\ScheduleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class ScheduleController extends AbstractController
{
     /**
     * @Route("/schedule", name="schedule")
     */
    public function index()
    {
        return $this->render('schedule/index.html.twig', [
            'controller_name' => 'ScheduleController',
        ]);
    }

     /**
     * @Route("/new", name="schedule_new", methods={"GET","POST"})
     */
    public function new(Request $request  ): Response
    {
        // MailerInterface $mailer
        $schedule = new Schedule();
        $form = $this->createForm(ScheduleType::class, $schedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $schedule->setVisible(false);
            $schedule->setDescription("Sin aprobar");
            $entityManager->persist($schedule);
            $entityManager->flush();

            // ENVIA UN CORREO AL ADMINISTRADOR PARA NOTIFICARLE QUE HAY UNA PROPUESTA 
            
            // $mensaje="Hay un nueva propuesta de horario";
            // $destino="jaimenavarrol97@gmail.com";
            // $email = (new Email())
            // ->from('postmaster@localhost')
            // ->to($destino)
            // ->subject('Nueva propuesta horaria');
            // // ->html($mensaje);

            // $mailer->send($email);





            return $this->redirectToRoute('main');
        }

        return $this->render('schedule/new.html.twig', [
            'schedule' => $schedule,
            'form' => $form->createView(),
        ]);
    }
}
