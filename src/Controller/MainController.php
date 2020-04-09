<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ContactType;
use App\Form\IncidenceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Swift_Mailer;

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
     * @Route("/incidence/{username}", name="incidence", methods={"GET","POST"})
     */
    public function incidence(Request $request, $username, Swift_Mailer $mailer)
    {
        $form = $this->createForm(IncidenceType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $repositoryUsers = $this->getDoctrine()->getRepository(User::class);
            $user = $repositoryUsers->findOneByUsername($username);

            // $descripcion=$_POST['form']['Descipcion'];

            //Enviar correo
            $message = (new \Swift_Message('Nueva Incidencia'))
                ->setFrom('postmaster@localhost')
                ->setTo($user->getEmail())
                ->setBody($_POST['incidence']['Descripcion']);

            $mailer->send($message);

            return $this->redirectToRoute('main');
            // return $this->render('main/debug.html.twig', [
            //     'form' => $_POST['incidence']['Descripcion'],
            // ]);
        }
        $user = $username;

        return $this->render('main/incidence.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * @Route("/contact", name="contact", methods={"GET","POST"})
     */
    public function contact(Request $request, Swift_Mailer $mailer)
    {
        //RECOGER LOS DATOS DEL FORMULARIO
        if (isset($_POST['Nombre'], $_POST['Correo'], $_POST['Telefono'], $_POST['Mensaje'])) {
            $nombre=$_POST['Nombre'];
            $correo=$_POST['Correo'];
            $telefono=$_POST['Telefono'];
            $mensaje=$_POST['Mensaje'];

            //Enviar correo
            $message = (new \Swift_Message('Nueva Consulta'))
            ->setFrom('postmaster@localhost')
            ->setTo("jaimenavarrol97@gmail.com")
            ->setBody("Nombre: ".$nombre." Correo: ".$correo." Telefono: ".$telefono." Mensaje: ".$mensaje);

            $mailer->send($message);
        }
        return $this->redirectToRoute('main');
      
    }
}
