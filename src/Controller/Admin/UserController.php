<?php

namespace App\Controller\Admin;

use App\ControllerHandler\Admin\UserControllerHandler;
use App\Entity\User;
use App\Form\Admin\Security\UserEditType;
use App\Message\Admin\UserUpdated;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/admin', name: 'api_admin_')]
final class UserController extends AbstractController
{
    public function __construct(private readonly UserControllerHandler $userControllerHandler) {}

    /**
     * @throws ExceptionInterface
     */
    #[Route('/user_edit', name: 'user_edit', methods: ['POST'])]
    public function edit(Request $request, MessageBusInterface $messageBus): JsonResponse
    {
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

        $this->userControllerHandler->userEdit($request->toArray(), $request->getHost());

        $messageBus->dispatch(new UserUpdated($data['id'], $data['username'], $data['email'], $data['role']));

        return new JsonResponse(['data' => $data], Response::HTTP_OK);
    }
}
