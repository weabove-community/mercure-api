<?php

namespace App\Controller\WeAbove;

use App\Service\Blockchain\ERC20\WeAbove\ProcessStakingGRV;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\ResponseInterface;

#[Route('/weabove/estimate-staking')]
class EstimateStaking extends AbstractController
{
    private $processStakingGRV;

    public function __construct(ProcessStakingGRV $processStakingGRV)
    {
        $this->processStakingGRV = $processStakingGRV;
    }

    #[Route('/{wallet}', name: 'weabove-estimate-staking')]
    public function process($wallet)
    {
        $prime = $this->processStakingGRV->getPrimeTokensFromWallet($wallet);
        $ordos = $this->processStakingGRV->getOrdosTokensFromWallet($wallet);
        $result = [
            'wallet-address' => $wallet,
            'total' => $prime['sum'] + $ordos['sum'],
            'prime' => $prime,
            'ordos' => $ordos
        ];

        return new JsonResponse($result);
    }
}
