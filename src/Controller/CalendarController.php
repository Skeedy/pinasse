<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Form\CalendarType;
use App\Repository\CalendarRepository;
use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/calendar")
 */
class CalendarController extends AbstractController
{
    /**
     * @Route("/", name="calendar_index", methods={"GET"})
     */
    public function index(CalendarRepository $calendarRepository): Response
    {
        return $this->render('calendar/index.html.twig', [
            'calendars' => $calendarRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="calendar_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('calendar_index');
        }

        return $this->render('calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_show", methods={"GET"})
     */
    public function show(Calendar $calendar): Response
    {
        return $this->render('calendar/show.html.twig', [
            'calendar' => $calendar,
        ]);
    }

    /**
     * @Route("/api/{id}/edit", name="api_calendar_edit", methods={"PUT"})
     */
    public function putEvent(?Calendar $calendar, Request $request){
        $data = json_decode($request->getContent());
        if(isset($data->title) && !empty($data->title) &&
        isset($data->start) && !empty($data->start) &&
        isset($data->end) && !empty($data->end) &&
        isset($data->background) && !empty($data->background) &&
        isset($data->border) && !empty($data->border) &&
        isset($data->color) && !empty($data->color) &&
        isset($data->description) && !empty($data->description))
        {
            if(!$calendar){
                $calendar = new Calendar();
            }
            $calendar->setTitle($data->title);
            $calendar->setDescription($data->description);
            $calendar->setBackground($data->background);
            $calendar->setStart(new DateTime($data->start));
            $calendar->setEnd($data->allDay? new DateTime($data->start) : new DateTime($data->end));
            $calendar->setAllDay($data->allDay);
            $calendar->setColor($data->color);
            $calendar->setBorder($data->border);
            $em= $this->getDoctrine()->getManager();
            $em->persist($calendar);
            $em->flush();
            return new Response('La date a été changé', 201);
        }else{
            return new Response('Données incomplètes', 404);
        }
    }
    /**
     * @Route("/{id}/edit", name="calendar_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Calendar $calendar): Response
    {
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('calendar_index');
        }

        return $this->render('calendar/edit.html.twig', [
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="calendar_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Calendar $calendar): Response
    {
        if ($this->isCsrfTokenValid('delete'.$calendar->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('calendar_index');
    }
}
