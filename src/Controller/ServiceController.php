<?php

namespace App\Controller;

use App\Entity\Service;
use App\Form\ServiceType;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/service")
 */
class ServiceController extends AbstractController
{
    /**
     * @Route("/", name="service_index", methods={"GET"})
     */
    public function index(ServiceRepository $serviceRepository): Response
    {
        return $this->render('service/index.html.twig', [
            'services' => $serviceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="service_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $service = new Service();
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $images = $service->getImages();
            $index= 0;
            $newimages = $form->get('images');
            foreach ($newimages as $newimage) {
                if ($file = $newimage['file']->getData()) {
                    $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                    // Move the file to the directory where brochures are stored
                    try {
                        $file->move(
                            $this->getParameter('img_abs_path'), $fileName
                        );
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                    $currentImage = $images[$index];
                    $currentImage->setPath($this->getParameter('img_abs_path') . '/' . $fileName);
                    $currentImage->setImgpath($this->getParameter('img_path') . '/' . $fileName);
                    $entityManager->persist($currentImage);
                    $index ++;

                }
                else {
                    $service->addImage(null);
                }
            }

            $entityManager->persist($service);
            $entityManager->flush();
            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/new.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_show", methods={"GET"})
     */
    public function show(Service $service): Response
    {
        return $this->render('service/show.html.twig', [
            'service' => $service,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="service_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Service $service): Response
    {
        $form = $this->createForm(ServiceType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $images = $service->getImages();
            $newImages = $form->get('images');
            foreach ($newImages as $newImage) {
                foreach ($images as $image) {
                    if ($file = $newImage['file']->getData()) {
                        $fileName = $this->generateUniqueFileName() . '.' . $file->guessExtension();
                        // Move the file to the directory where brochures are stored
                        try {
                            $file->move(
                                $this->getParameter('img_abs_path'), $fileName
                            );
                        } catch (FileException $e) {
                            // ... handle exception if something happens during file upload
                        }
                        $this->removeFile($image->getPath());
                        $image->setPath($this->getParameter('img_abs_path') . '/' . $fileName);
                        $image->setImgpath($this->getParameter('img_path') . '/' . $fileName);
                        $entityManager->persist($image);
                    }
                    if ($image && empty($image->getId()) && !$file) {
                        $service->addImage(null);
                    }
                    $this->getDoctrine()->getManager()->flush();
                }
            }
            return $this->redirectToRoute('service_index');
        }

        return $this->render('service/edit.html.twig', [
            'service' => $service,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="service_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Service $service): Response
    {
        if ($this->isCsrfTokenValid('delete'.$service->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $images = $service->getImages();
            foreach ($images as $image)
                if($image) {
                    $this->removeFile($image->getPath());
                    $entityManager->remove($image);
                }
            $entityManager->remove($service);
            $entityManager->flush();
        }

        return $this->redirectToRoute('service_index');
    }
    function generateUniqueFileName() {
        return md5(uniqid());
    }
    private function removeFile($path){
        if(file_exists($path)){
            unlink($path);
        }
    }
}
