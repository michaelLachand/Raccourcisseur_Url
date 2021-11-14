<?php

namespace App\Controller;

use App\Entity\Url;
use App\Form\UrlFormType;
use App\Repository\UrlRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UrlsController extends AbstractController
{
    /**
     * @Route("/", name="app_home",methods = {"GET", "POST"})
     * @Route("/", name="app_urls_create",methods = {"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param UrlRepository $urlRepository
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em, UrlRepository $urlRepository): Response
    {
            $form = $this->createForm(UrlFormType::class);


            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $url = $urlRepository->findOneBy(['original' => $form['original']->getData()]);

                if (!$url){
                    $url = $form->getData();
                    $em->persist($url);
                    $em->flush();
                }

                return $this->redirectToRoute('app_urls_preview', ['shortened' => $url->getShortened()]);
            }

        return $this->render('urls/create.html.twig',[
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{shortened}/preview", name="app_urls_preview", methods= "GET")
     * @param Url $url
     * @return Response
     */
    public function preview(Url $url): Response
    {
        return $this->render('urls/preview.html.twig', compact('url'));
    }

    /**
     * @Route("/{shortened}", name="app_urls_show", methods= "GET")
     * @param Url $url
     * @return Response
     */
    public function show(Url $url): Response
    {
        return $this->redirect($url->getOriginal());
    }


}
