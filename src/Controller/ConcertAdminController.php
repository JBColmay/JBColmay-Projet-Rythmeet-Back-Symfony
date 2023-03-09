<?php

namespace App\Controller;

use App\Entity\Concert;
use App\Form\ConcertType;
use App\Repository\ConcertRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/concert")
 */
class ConcertAdminController extends AbstractController
{
    /**
     * @Route("/", name="app_concert_admin_index", methods={"GET"})
     */
    public function index(ConcertRepository $concertRepository): Response
    {
        return $this->render('concert_admin/index.html.twig', [
            'concerts' => $concertRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_concert_admin_new", methods={"GET", "POST"})
     */
    public function new(Request $request, ConcertRepository $concertRepository): Response
    {
        $concert = new Concert();
        $form = $this->createForm(ConcertType::class, $concert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $concertRepository->add($concert, true);

            return $this->redirectToRoute('app_concert_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('concert_admin/new.html.twig', [
            'concert' => $concert,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_concert_admin_show", methods={"GET"})
     */
    public function show(Concert $concert): Response
    {
        return $this->render('concert_admin/show.html.twig', [
            'concert' => $concert,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_concert_admin_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Concert $concert, ConcertRepository $concertRepository): Response
    {
        $form = $this->createForm(ConcertType::class, $concert);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $concertRepository->add($concert, true);

            return $this->redirectToRoute('app_concert_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('concert_admin/edit.html.twig', [
            'concert' => $concert,
            'form' => $form,
        ]);
    }

    /**
     * @Route("/{id}", name="app_concert_admin_delete", methods={"POST"})
     */
    public function delete(Request $request, Concert $concert, ConcertRepository $concertRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$concert->getId(), $request->request->get('_token'))) {
            $concertRepository->remove($concert, true);
        }

        return $this->redirectToRoute('app_concert_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
