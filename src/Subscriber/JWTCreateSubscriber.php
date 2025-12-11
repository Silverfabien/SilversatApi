<?php

namespace App\Subscriber;

use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class JWTCreateSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [JWTCreatedEvent::class => 'onJWTCreated'];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        /* @var User $user */
        $user = $event->getUser();
        $data = $event->getData();

        $data['roles'] = $user->getRolesAllSite();
        $data['email'] = $user->getEmail();

        $event->setData($data);
    }
}
