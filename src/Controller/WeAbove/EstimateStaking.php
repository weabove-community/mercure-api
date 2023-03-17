<?php

namespace App\Controller\WeAbove;

use App\Service\Blockchain\ERC20\WeAbove\ProcessStakingGRV;
use App\Service\Blockchain\ERC20\WeAbove\StatusService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/weabove/estimate-staking')]
class EstimateStaking extends AbstractController
{
    private $processStakingGRV;

    public function __construct(ProcessStakingGRV $processStakingGRV,
                                StatusService $statusService)
    {
        $this->processStakingGRV = $processStakingGRV;
        $this->statusService = $statusService;
    }

    #[Route('/{wallet}', name: 'weabove-estimate-staking')]
    public function process($wallet)
    {
        $res = $this->processStakingGRV->getTokensFromWallet($wallet);
        $prime = $res['prime'];
        $ordos = $res['ordos'];
        $lore = $res['lore'];
        $sum = $prime['sum'] + $ordos['sum'] + $lore['sum'];
        $result = [
            'wallet-address' => $wallet,
            'overflow' => $sum - 200 > 0 ? $sum - 200 : 0,
            'status' => $this->statusService->define($sum),
            'total' => $sum,
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
