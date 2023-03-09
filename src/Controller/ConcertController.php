<?php

namespace App\Controller;


use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Concert;
use Symfony\Config\Doctrine\Orm\EntityManagerConfig;

class ConcertController extends AbstractController
{
    /**
     * ajouter un concert
     * 
     * @Route("/api/concert/create", name="app_concert_create", methods={"POST"})
     */
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $body = json_decode($request->getContent(), true);

        $concert = new Concert();

        $dateString = $body['date'];
        $date = DateTime::createFromFormat('Y-m-d', $dateString);

        $user = $this->getUser();

        $concert->setArtistName($body['artist_name'])
            ->setPlace($body['place'])
            ->setDate($date)
            ->setCreatedAt(new DateTime())
            ->setUser($user);

        
        

        $em->persist($concert);
        $em->flush();
        return $this->json(["message" => "le concert a été créé", "nom" =>
        $concert->getArtistName()]);
    }


    /**
     * affichage des concerts
     * 
     * @Route("api/concerts", name="app_concert_list", methods={"GET"})
     */
    public function concertsList(EntityManagerInterface $em): Response
    {
        return $this->json(['concerts' => $em->getRepository(Concert::class)->findAll()], 200, [], ['groups' => 'concerts:read']);
    }


    /**
     * inscription concert
     * 
     * @Route("api/concert/{id}/subscribe", name="app_concert_subscribe", methods={"POST"})
     */
    public function inscription(Concert $concert, EntityManagerInterface $em)
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $concert->addSubscriber($user);
        $concertName = $concert->getArtistName();

        $em->persist($concert);
        $em->flush();

        // Retour d'une réponse de confirmation
        return $this->json(['message' => 'Inscription effectuée avec succès au concert de ' . $concertName, 'concert' => $concert], 200, [], ['groups' => 'concert:read']);
    }

/**
 * Désinscription concert
 * 
 * @Route("api/concert/{id}/unsubscribe", name="app_concert_unsubscribe", methods={"POST"})
 */
public function desinscription(Concert $concert, EntityManagerInterface $em)
{
    /** @var \App\Entity\User $user */
    $user = $this->getUser();

    $concert->removeSubscriber($user);
    $concertName = $concert->getArtistName();

    $em->persist($concert);
    $em->flush();

    // Retour d'une réponse de confirmation
    return $this->json(['message' => 'Désinscription effectuée avec succès du concert de ' . $concertName]);
}




    /**
     * Route qui affiche les infos d'un concert selon son ID
     * 
     * @Route("api/concert/{id}", name="api_concert_id", methods={"GET"})
     */
    public function showConcert(EntityManagerInterface $em, $id): Response
    {
        return $this->json(['concert' => $em->getRepository(Concert::class)->findById($id)], 200, [], ['groups' => 'concert:read']);
    }

    
}

    