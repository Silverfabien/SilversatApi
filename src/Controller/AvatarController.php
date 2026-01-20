<?php

namespace App\Controller;

use App\Repository\UserInfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class AvatarController extends AbstractController
{
    #[Route('/user/{id}/avatar', name: 'user_avatar', methods: ['GET'])]
    public function avatar(int $id, UserInfoRepository $userInfoRepository): Response
    {
        $user = $userInfoRepository->findOneBy(['user' => $id]);

        if (!$user || !$user->getPictureName()) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        $filePath = $this->getParameter('kernel.project_dir').'/public/uploads/pictures/users/'.$user->getPictureName();

        if (!file_exists($filePath)) {
            return new Response(null, Response::HTTP_NO_CONTENT);
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);

        return $response;
    }
}
