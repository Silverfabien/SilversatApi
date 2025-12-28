<?php

namespace App\Controller;

use App\ControllerHandler\UserControllerHandler;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Security\JWTSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly UserControllerHandler $userControllerHandler,
        private readonly JWTSuccessHandler $jwtSuccessHandler
    ) {}

    #[Route('/user_edit', name: 'user_edit', methods: ['POST'])]
    public function userEdit(Request $request): JsonResponse
    {
        $user = $this->decodeJwt($request);

        $this->userControllerHandler->userEdit($user, $request->toArray());

        return $this->generateResponse(
            'Vos informations on bien été modifié.',
            $user
        );
    }

    #[Route('/reset_password', name: 'reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $user = $this->decodeJwt($request);

        $this->userControllerHandler->resetPassword($user, $request->toArray());

        return $this->generateResponse(
            'Votre mot de passe a bien été modifié.',
            $user
        );
    }

    private function decodeJwt(Request $request): ?User
    {
        $jwt = $request->cookies->get('jwt_token');
        $decodeJwt = json_decode(base64_decode(explode('.', $jwt)[1]), true);
        $user = $this->userRepository->findOneBy(['email' => $decodeJwt['email']]);

        if (!$user) {
            return null;
        }

        return $user;
    }

    private function generateResponse(string $message, User $user): JsonResponse
    {
        $response = $this->jwtSuccessHandler->generateJwtResponse($user);
        $content['message'] = $message;
        $response->setData($content);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }
}
