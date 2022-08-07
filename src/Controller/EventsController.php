<?php

namespace App\Controller;

use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Repository\EventsRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Location;
use App\Entity\Events;
use App\Form\EventsType;
use Symfony\Contracts\EventDispatcher\Event;

class EventsController extends AbstractController
{
    #[Route('/index', name: 'events')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $events = $doctrine->getRepository(Events::class)->findAll();
        return $this->render('events/index.html.twig', [
            'controller_name' => 'EventsController', "events" => $events
        ]);
    }

    #[Route('/', name: 'eventsg')]
    public function home(ManagerRegistry $doctrine): Response
    {
        $eventsg = $doctrine->getRepository(Events::class)->findAll();
        return $this->render('events/home.html.twig', [
            'controller_name' => 'EventsController', "events" => $eventsg
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EventsRepository $eventsRepository, ManagerRegistry $doctrine, FileUploader $fileUploader): Response
    {
        $event = new Events();
        $form = $this -> createForm(EventsType::class, $event);

        $form -> handleRequest($request);
        if ($form -> isSubmitted() && $form -> isValid()) {

            $pictureFile = $form->get('photo')->getData();
            if($pictureFile){
                $pictureFileName = $fileUploader->upload($pictureFile);
                $event->setPhoto($pictureFileName);
            }
            // $now = new \DateTime('now');
            $event = $form -> getData();
            $em = $doctrine -> getManager();
            $em -> persist($event);
            // $location = $doctrine -> getRepository(Location::class)->find(1);
            // $event -> setFkLocation($location);
            $em -> flush();

            $eventsRepository -> add($event);
            $this -> addFlash(
                'notice',
                'Event Added'
            );
            return $this -> redirectToRoute('events');
        }

        return $this->render('events/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(Request $request, ManagerRegistry $doctrine, $id): Response
    {
        $event = $doctrine -> getRepository(Events::class) -> find($id);
        $form = $this -> createForm(EventsType::class, $event);

        $form -> handleRequest($request);

        if($form -> isSubmitted() && $form -> isValid()) {

            $event = $form -> getData();
            $em = $doctrine -> getManager();
            $em -> persist($event);
            $em -> flush();
            $this -> addFlash(
                'notice',
                'Event Edited'
            );
            return $this -> redirectToRoute('events');
        }
        return $this->render('events/edit.html.twig', [
            'form' => $form->createView()
        ]);

    }#[Route('/details/{id}', name: 'details')]
    public function details(ManagerRegistry $doctrine, $id): Response
    {
        $event = $doctrine -> getRepository(Events::class) -> find($id);
        // dd($event);
        return $this->render('events/details.html.twig', [
            'controller_name' => 'EventsController', "event" => $event
        ]);
    }
    #[Route('/delete/{id}', name: 'delete')]
  public function delete ($id, ManagerRegistry $doctrine){
      $em = $doctrine -> getManager();
      $event = $em->getRepository(Events::class)->find($id);
      $em->remove($event);

      $em->flush();
      $this->addFlash(
          'notice',
          'Event Removed'
      );
      return $this->redirectToRoute('events');
}

    #[Route('/about', name: 'about')]
   public function about(): Response
   {
       return $this->render('events/about.html.twig', [
           'controller_name' => 'EventsController',
       ]);
   } 
   

   #[Route('/contact', name: 'contact')]
   public function contact(): Response
   {
       return $this->render('events/contact.html.twig', [
           'controller_name' => 'EventsController',
       ]);
   }
   
}
