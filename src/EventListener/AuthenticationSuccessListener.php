<?php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class AuthenticationSuccessListener
{
    private $serializer;
    private $jwtEncoder;

    public function __construct(SerializerInterface $serializer, JWTEncoderInterface $jwtEncoder)
    {
        $this->serializer = $serializer;
        $this->jwtEncoder = $jwtEncoder;
    }

    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event)
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof UserInterface) {
            return;
        }

        // on ajoute les infos qu'on veut du user authentifié dans les données (data)
        $data['user'] = $this->serializer->normalize($user, true, ['groups' => 'user:me']);

        // on ajoute la date d'expiration du token dans les données (data)
        $data['token_expires'] = $this->jwtEncoder->decode($data['token'])['exp'];

        // on reset les data mises à jours dans les données de l'event
        $event->setData($data);
    }
}
