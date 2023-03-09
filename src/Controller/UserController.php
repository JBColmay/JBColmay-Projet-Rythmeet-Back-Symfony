<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

class UserController extends AbstractController
{
    /**
     * inscription de l'utilisateur
     *
     * @Route("/api/register", name="api_register")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        //1) on récupère les valeurs simples envoyées par le front sous forme de tableau key=>value
        $data = $request->request->all();

        //2) on encode le tableau de données en json pour pouvoir le mettre facilement dans un new user avec le serializer
        $user = $serializer->deserialize(json_encode($data), User::class, 'json', ['object_to_populate' => new User()]);

        $user->setImageFile($request->files->get('img'));
        //3) on valide l'objet User en utilisant le validator (il va se servir des annotations assert de l'entité User pour s'assurer que les données sont valides)
        $errors = $validator->validate($user);

        //4) si il y a des erreurs, on les renvoie au format json
        if (count($errors) > 0) {
            return $this->json($errors, 400);
        }

        //5) si tout est ok, on hash le mot de passe, on set le nouveau mdp hashé à la place de l'ancien, on persiste et on flush
        $user->setCreatedAt(new DateTime());
        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);
        $em->persist($user);
        $em->flush();

        //6) on renvoie un message de succès
        return $this->json($user, 200, [], ['groups' => 'user:me']);
    }

    /**
     * page de l'utilisateur
     *
     * @Route("/api/me", name="api_me", methods={"GET"})
     */
    public function me(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        return $this->json($user, 200, [], ['groups' => 'user:me']);
    }

    /**
     * affichage d'un utilisateur
     *
     * @Route("/api/user/{id}", name="api_user_id", methods={"GET"})
     */
    public function showUser(User $user): Response
    {
        return $this->json($user, 200, [], ['groups' => 'user:read']);
    }


    /**
     *edition du profil de l'utilisateur
     *
    * @Route("/api/me", name="edit_profile", methods={"POST"})
    */
public function edit(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, SerializerInterface $serializer, ValidatorInterface $validator, Security $security)
{   
    /** @var \App\Entity\User $user */
    $user = $security->getUser();

    $data = $request->request->all();
    $file = $request->files->get('img');

    $serializer->deserialize(json_encode($data), User::class, 'json', ['object_to_populate' => $user, AbstractObjectNormalizer::IGNORED_ATTRIBUTES => ['password']]);

    if($file) $user->setImageFile($file);

    $errors = $validator->validate($user);

    if (count($errors) > 0) {
        return $this->json($errors, 400);
    }

    if (isset($data['password'])) {
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
    }

    $em->flush();

    return $this->json($user, 200, [], ['groups' => 'user:me']);
}

    /**
     * suppression du profil de l'utilisateur
     *
    * @Route("/api/me", name="delete_profile", methods={"DELETE"})
    */
    public function deleteMe(UserRepository $userRepository): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        // Supprimer l'utilisateur de la base de données
        $userRepository->remove($user, true);

        // Retourner une réponse JSON avec un message de confirmation
        return $this->json(['message' => 'Votre compte a été supprimé.']);
    }

/**
 * Route pour afficher la liste des concerts auxquels l'utilisateur participe
 *
 * @Route("api/me/concertslist", name="api_me_concertslist", methods={"GET"})
 */
public function inscriptionList(): Response
{

    /** @var \App\Entity\User $user */
    // Récupère l'utilisateur connecté
    $user = $this->getUser();
    
    // Récupère la liste des concerts auxquels l'utilisateur est inscrit
    $concerts = $user->getConcerts();

    // Retourne un objet JSON contenant la liste des concerts
    return $this->json($concerts, 200, [], ['groups' => 'concert:list:me']);
}


}