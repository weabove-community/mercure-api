<?php

namespace App\Controller\App;

use App\Repository\TokenAttributeRepository;
use App\Repository\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    #[Route('/tokens/{token}', name: 'app_token_index')]
    public function index(
        TokenRepository $tokenRepository,
        TokenAttributeRepository $tokenAttributeRepository,
        int $token): Response
    {
        $token = $tokenRepository->findOneByToken($token);
        $tokenAttributes = $tokenAttributeRepository->findByToken($token);

        return $this->render('token/index.html.twig', [
            'token' => $token,
            'tokenAttributes' => $tokenAttributes
        ]);
    }

}
