<?php

namespace App\Controller;

use App\ControllerHandler\SecurityControllerHandler;
use App\Entity\User;
use App\Form\UserType;
use App\Message\UserCreated;
use App\Repository\UserRepository;
use App\Repository\UserSecurityRepository;
use App\Security\JWTSuccessHandler;
use DateTimeImmutable;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/api', name: 'api_')]
final class SecurityController extends AbstractController
{
    public function __construct(
        private readonly SecurityControllerHandler $securityControllerHandler,
        private readonly UserSecurityRepository $userSecurityRepository,
        private readonly UserRepository $userRepository,
        private readonly ParameterBagInterface $params,
        private readonly MailerInterface $mailer
    ) {}

    #[Route('/check_token', name: 'check_token', methods: ['GET'])]
    public function checkToken():JsonResponse
    {
        return new JsonResponse(["status" => "Ok"], Response::HTTP_OK);
    }

    /**
     * @throws ExceptionInterface
     * @throws TransportExceptionInterface
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(
        Request $request,
        FormFactoryInterface $formFactory,
        MessageBusInterface $messageBus,
        JWTSuccessHandler $jwtSuccessHandler
    ): JsonResponse
    {
        $user = new User();
        $form = $formFactory->create(UserType::class, $user);
        $data = json_decode($request->getContent(), true);
        $form->submit($data);

        if (!$form->isValid()) {
            $errors = [];
            foreach ($form->getErrors(true) as $error) {
                $errors[] = [
                    'field' => $error->getOrigin()->getName(),
                    'message' => $error->getMessage(),
                ];
            }

            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->securityControllerHandler->createUser($user);

        $messageBus->dispatch(new UserCreated($user->getId(), $user->getUsername(), $user->getEmail()));

        $this->sendVerificationMail($user);

        // Permet la connexion automatiquement après inscription.
        $response = $jwtSuccessHandler->generateJwtResponse($user);
        $content = json_decode($response->getContent(), true);
        $content['message'] = "Compte créer avec succès.";
        $response->setData($content);
        $response->setStatusCode(Response::HTTP_CREATED);

        return $response;
    }

    #[Route('/confirm/{token}', name: 'confirm', methods: ['GET', 'POST'])]
    public function confirmationAccount(string $token, Request $request): JsonResponse
    {
        $jwt = $request->cookies->get('jwt_token');

        if (!$jwt) {
            return new JsonResponse(['message' => "Veuillez vous connecter pour valider votre compte.", 'jwt' => $jwt], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->userSecurityRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            return new JsonResponse(['message' => "Votre compte à déjà été vérifié."], Response::HTTP_BAD_REQUEST);
        } elseif ($user->getConfirmationTokenExpirationAt() < new DateTimeImmutable()) {
            return new JsonResponse(['message' => "Token invalide ou expiré."], Response::HTTP_BAD_REQUEST);
        }

        $this->securityControllerHandler->verifyAccount($user);

        return new JsonResponse(['message' => "Votre compte à bien été validé."], Response::HTTP_OK);
    }

    #[Route('/remove-account/{token}', name: 'remove_account', methods: ['GET', 'DELETE'])]
    public function removeAccount(string $token): JsonResponse
    {
        $user = $this->userSecurityRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            return new JsonResponse(['message' => "Token invalide ou expiré."], Response::HTTP_BAD_REQUEST);
        }

        $this->securityControllerHandler->deleteAccount($user);

        return new JsonResponse(['message' => "Votre compte à bien été supprimé."], Response::HTTP_OK);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('/reply', name: 'reply', methods: ['GET', 'POST'])]
    public function replyMail(Request $request): JsonResponse
    {
        $jwt = $request->cookies->get('jwt_token');
        $decodeJwt = json_decode(base64_decode(explode('.', $jwt)[1]), true);
        $user = $this->userRepository->findOneBy(['email' => $decodeJwt['email']]);

        if (!$user) {
            return new JsonResponse(['message' => "Veuillez vous connecter pour renvoyer le mail de validation."], Response::HTTP_BAD_REQUEST);
        } elseif (!$user->getUserSecurity()->getConfirmationToken()) {
            return new JsonResponse(['message' => "Votre compte est déjà vérifié."], Response::HTTP_BAD_REQUEST);
        }

        $this->sendVerificationMail($user);

        return new JsonResponse(['message' => "Email de confirmation renvoyé."], Response::HTTP_OK);
    }


    /**
     * @throws TransportExceptionInterface
     */
    private function mail(User $user): void
    {
        $search = $this->userSecurityRepository->findOneBy(['user' => $user->getId()]);

        $urlConfirm = $this->generateUrl('api_confirm', ['token' => $search->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);
        $urlRemove = $this->generateUrl('api_remove_account', ['token' => $search->getConfirmationToken()], UrlGeneratorInterface::ABSOLUTE_URL);

        $from = $this->params->get('email_from');

        $mail = (new TemplatedEmail())
            ->from($from)
            ->to($user->getEmail())
            ->subject('Confirmation de votre compte')
            ->htmlTemplate('mail/confirmation.html.twig')
            ->context([
                'user' => $user,
                'urlConfirm' => $urlConfirm,
                'urlRemove' => $urlRemove,
            ]);

        $this->mailer->send($mail);
    }

    /**
     * @throws TransportExceptionInterface
     */
    private function sendVerificationMail(User $user): void
    {
        $this->securityControllerHandler->sendMail($user);
        $this->mail($user);
    }
}
