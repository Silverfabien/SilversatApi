<?php

namespace App\Controller;

use App\ControllerHandler\UserControllerHandler;
use App\Entity\User;
use App\Form\Security\ResetPasswordType;
use App\Form\Security\SoftDeletedAccountType;
use App\Form\Security\UserEditType;
use App\Message\UserSoftDeleted;
use App\Message\UserUpdated;
use App\Security\JWTSuccessHandler;
use DateTimeImmutable;
use Random\RandomException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
final class UserController extends AbstractController
{
    public function __construct(
        private readonly UserControllerHandler $userControllerHandler,
        private readonly JWTSuccessHandler $jwtSuccessHandler
    ) {}

    /**
     * @throws ExceptionInterface
     */
    #[Route('/user_edit', name: 'user_edit', methods: ['POST'])]
    public function userEdit(Request $request, MessageBusInterface $messageBus): JsonResponse
    {
        $user = $this->decodeJwt();
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(UserEditType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse([
                'message' => $errors,
                'type' => 'error'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->userControllerHandler->userEdit($user, $request->toArray());

        $messageBus->dispatch(new UserUpdated($user->getId(), $user->getUsername(), $user->getEmail()));

        return $this->generateResponse(
            'success',
            'Vos informations on bien été modifié.',
            $user
        );
    }

    #[Route('/reset_password', name: 'reset_password', methods: ['POST'])]
    public function resetPassword(Request $request): JsonResponse
    {
        $user = $this->decodeJwt();
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(ResetPasswordType::class, $user);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse([
                'message' => $errors,
                'type' => 'error'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->userControllerHandler->resetPassword($user);

        return $this->generateResponse(
            'success',
            'Votre mot de passe a bien été modifié.',
            $user
        );
    }

    /**
     * @throws RandomException
     * @throws ExceptionInterface
     */
    #[Route('/soft_delete', name: 'soft_delete', methods: ['POST'])]
    public function softDelete(Request $request, MessageBusInterface $messageBus): JsonResponse
    {
        $user = $this->decodeJwt();
        $data = json_decode($request->getContent(), true);

        $form = $this->createForm(SoftDeletedAccountType::class);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = $error->getMessage();
            }

            return new JsonResponse([
                'message' => $errors,
                'type' => 'error'
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->userControllerHandler->softDelete($user, $data);

        $messageBus->dispatch(new UserSoftDeleted($user->getId(), $user->getUsername(), $user->getEmail()));

        $response = new JsonResponse([
            "message" => "Votre compte à été soft delete et vous êtes dorénavant déconnecté.",
            "type" => "success"
        ], Response::HTTP_OK);

        $this->deleteCookie($response);

        return $response;
    }

    private function decodeJwt(): ?User
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return null;
        }

        return $user;
    }

    private function generateResponse(string $type, string $message, User $user): JsonResponse
    {
        $response = $this->jwtSuccessHandler->generateJwtResponse($user);
        $content['type'] = $type;
        $content['message'] = $message;
        $response->setData($content);
        $response->setStatusCode(Response::HTTP_OK);

        return $response;
    }

    private function deleteCookie(Response $response): void
    {
        $response->headers->setCookie(
            Cookie::create(
                'jwt_token',
                null,
                new DateTimeImmutable('-1 hour'),
                '/',
                '127.0.0.1',
                true,
                true,
                false,
                Cookie::SAMESITE_NONE
            )
        );
    }
}
