<?php

namespace App\Controller;

use App\Service\Alchemy\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/collections")
 */
class CollectionController extends AbstractController
{
    /**
     * @Route("/{collectionIdentifier}/floor-price", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Return floor price collection"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @OA\Parameter(
     *     in="path", name="collectionIdentifier", description="Collection identifier", @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Collections")
     */
    public function getFloorPrice(Client $client, $collectionIdentifier): Response
    {
        $response = $client->getFloorPrice($collectionIdentifier);
        $response =  new Response($response->getBody()->getContents(), Response::HTTP_OK, []);
        $response->headers->set('Access-Control-Allow-Methods', 'GET');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
