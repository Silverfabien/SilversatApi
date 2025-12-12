<?php

namespace App\Subscriber;

use App\Entity\User;
use App\Repository\UserInfoRepository;
use App\Repository\UserModRepository;
use App\Repository\UserSecurityRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class JWTCreateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private ParameterBagInterface $params,
        private UserModRepository $userModRepository,
        private UserSecurityRepository $userSecurityRepository,
        private UserInfoRepository $userInfoRepository
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [JWTCreatedEvent::class => 'onJWTCreated'];
    }

    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $passphrase = $this->params->get('check_pass');

        /* @var User $user */
        $user = $event->getUser();
        $data = $event->getData();

        $userMod = $this->userModRepository->findOneBy(['user' => $user]);
        $userSecurity = $this->userSecurityRepository->findOneBy(['user' => $user]);
        $userInfo = $this->userInfoRepository->findOneBy(['user' => $user]);

        $data['id'] = $user->getId();
        $data['roles'] = $user->getRolesAllSite();
        $data['email'] = $user->getEmail();
        $data['username'] = $user->getUsername();

        $otherInfo = [
            "firstname" => $userInfo?->getFirstname() ?? null,
            "lastname" => $userInfo?->getLastname() ?? null,
            "status" => $userMod->getStatus(),
            "accountDeleted" => $userMod->isAccountDeleted()
        ];

        $jsonData = json_encode($otherInfo);
        $decodeIv = base64_decode($userSecurity->getIv());
        $encodeData = openssl_encrypt($jsonData, 'aes-256-cbc', $passphrase, 0, $decodeIv);
        $data['other'] = base64_encode($encodeData);

        $event->setData($data);
    }
}
