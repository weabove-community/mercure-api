<?php

namespace App\Controller\App;

use App\Repository\TokenAttributeRepository;
use App\Repository\TokenRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TokenController extends AbstractController
{
    #[Route('/tokens/{token}', name: 'app_token_show')]
    public function show(
        TokenRepository $tokenRepository,
        TokenAttributeRepository $tokenAttributeRepository,
        int $token): Response
    {
        $token = $tokenRepository->findOneByToken($token);
        $tokenAttributes = $tokenAttributeRepository->findByToken($token);

        return $this->render('app/token/show.html.twig', [
            'token' => $token,
            'tokenAttributes' => $tokenAttributes
        ]);
    }

    #[Route('/tokens', name: 'app_token_index')]
    public function index(TokenRepository $tokenRepository): Response
    {
        $tokens = $tokenRepository->findBy([], [], 50);

        return $this->render('app/token/show.html.twig', [
            'tokens' => $tokens
        ]);
    }
}
