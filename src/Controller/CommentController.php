<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Concert;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends AbstractController
{



    /**
     * création commentaire
     * 
     * @Route("api/comment/{concertId}", name="app_commentaire_add", methods={"POST"})
     */
    public function addComment(Request $request, EntityManagerInterface $em, int $concertId): Response
    {
        $body = json_decode($request->getContent(), true);

        // récupérer l'objet Concert à partir de l'ID
        $concert = $em->getRepository(Concert::class)->find($concertId);

        $comment = new Comment();

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $comment->setContent($body['content'])
            ->setUser($user)
            ->setCreatedAt(new DateTime())
            ->setConcert($concert);

        $em->persist($comment);
        $em->flush();
        return $this->json(["commentaire" => $comment->getContent(), "pseudo" => $user->getPseudo(), "date" => $comment->getCreatedAt()]);
    }
    /**
     * @Route("/api/comment/{id}", name="delete_comment", methods={"DELETE"})
     */
    public function deleteComment(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupérer l'ID du commentaire à supprimer depuis la requête
        $commentId = $request->get('id');

        // Récupérer le commentaire correspondant à l'ID depuis la base de données
        $comment = $entityManager->getRepository(Comment::class)->find($commentId);

        // Vérifier si le commentaire existe
        if (!$comment) {
            return $this->json(['error' => 'Comment not found.'], Response::HTTP_NOT_FOUND);
        }

        // Supprimer le commentaire de la base de données
        $entityManager->remove($comment);
        $entityManager->flush();

        // Retourner une réponse JSON indiquant que le commentaire a été supprimé avec succès
        return $this->json(['message' => 'Comment deleted successfully.'], Response::HTTP_OK);
    }
}
