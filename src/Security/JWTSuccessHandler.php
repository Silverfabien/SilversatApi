<?php

namespace App\Security;

use App\ControllerHandler\SecurityControllerHandler;
use App\Entity\User;
use DateTimeImmutable;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

readonly class JWTSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private JWTTokenManagerInterface $jwtManager,
        private EventDispatcherInterface $eventDispatcher,
        private string $cookieDomain,
        private SecurityControllerHandler $securityControllerHandler,
        private RequestStack $requestStack
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): Response
    {
        /* @var User $user */
        $user = $token->getUser();

        $request = $this->requestStack->getCurrentRequest();
        $data = json_decode($request->getContent(), true);
        $this->securityControllerHandler->login($user, $data['url']);

        return $this->generateJwtResponse($user);
    }

    public function generateJwtResponse(User $user): JsonResponse
    {
        $payload = [];

        $event = new JWTCreatedEvent($payload, $user);
        $this->eventDispatcher->dispatch($event, JWTCreatedEvent::class);

        $jwt = $this->jwtManager->createFromPayload($user, $event->getData());

        $data = ['token' => $jwt];

        $response = new JsonResponse($data);

        $response->headers->setCookie(
            Cookie::create(
                'jwt_token',
                $jwt,
                new DateTimeImmutable('+7 days'),
                '/',
                $this->cookieDomain,
                true,
                true,
                false,
                Cookie::SAMESITE_NONE
            )
        );

        return $response;
    }
}
