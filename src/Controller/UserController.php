<?php

namespace App\Controller;

use App\ControllerHandler\UserControllerHandler;
use App\Entity\User;
use App\Form\Security\ResetPasswordType;
use App\Form\Security\UserEditType;
use App\Message\UserUpdated;
use App\Repository\UserRepository;
use App\Security\JWTSuccessHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
        $user = $this->decodeJwt($request);
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
        $user = $this->decodeJwt($request);
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
}
