<?php

namespace App\Controller;

use App\Repository\CalendarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendrierController extends AbstractController
{
    /**
     * @Route("/admin/calendrier", name="calendrier")
     */
    public function index(CalendarRepository $calendarRepository): Response
    {
        $events = $calendarRepository->findAll();
        $rdvs = [];
        foreach ($events as $event){
            $rdvs[] = [
                'id' =>$event->getId(),
                'start' =>$event->getStart()->format('Y-m-d H:i:s'),
                'end' =>$event->getEnd()->format('Y-m-d H:i:s'),
                'allDay' =>$event->getAllDay(),
                'backgroundColor' =>$event->getBackground(),
                'borderColor' =>$event->getBorder(),
                'textColor' =>$event->getColor(),
                'title'=>$event->getTitle(),
                'description' => $event->getDescription()
            ];
        }
        $data = json_encode($rdvs);
        return $this->render('calendrier/index.html.twig', compact('data'));
    }
}
