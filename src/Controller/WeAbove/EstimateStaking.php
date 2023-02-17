<?php

namespace App\Controller\WeAbove;

use App\Service\Blockchain\ERC20\WeAbove\ProcessStakingGRV;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
        $res = $this->processStakingGRV->getTokensFromWallet($wallet);
        $prime = $res['prime'];
        $ordos = $res['ordos'];
        $lore = $res['lore'];
        $result = [
            'wallet-address' => $wallet,
            'total' => $prime['sum'] + $ordos['sum'] + $lore['sum'],
            'prime' => $prime,
            'ordos' => $ordos,
            'lore' => $lore,
        ];
        $response = new JsonResponse($result);
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('Access-Control-Allow-Methods', 'GET');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
