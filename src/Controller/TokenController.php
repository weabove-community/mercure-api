<?php

namespace App\Controller;

use App\Entity\Token;
use App\Repository\TokenRepository;
use App\Service\AttributeService;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\Exception\JsonException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Hateoas\HateoasBuilder;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

/**
 * @Route("/api/tokens")
 */
class TokenController extends AbstractController
{
    /** @var TokenRepository */
    private $tokenRepository;

    /** @var AttributeService */
    private $attributeService;

    public function __construct(TokenRepository $tokenRepository, AttributeService $attributeService)
    {
        $this->tokenRepository = $tokenRepository;
        $this->attributeService = $attributeService;
    }

    /**
     * @Route("/{collectionIdentifier}/{token}", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return token by collection"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @OA\Parameter(
     *     in="path", name="collectionIdentifier", description="Collection identifier", @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     in="path", name="token", description="Collection identifier", @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Tokens")
     */
    public function get($collectionIdentifier, $token)
    {
        $tokenObject = $this->tokenRepository->findOneByCollectionIdentifierAndToken($collectionIdentifier, $token);
        if (null === $tokenObject) {
            throw new JsonException(
                sprintf('Token %s from identifier %s not found', $token, $collectionIdentifier),
                Response::HTTP_NOT_FOUND
            );
        }

        $hateoas = HateoasBuilder::create()->build();
        $content = $hateoas->serialize($token, 'json');

        $data = json_decode($content, true);
        $data['attributes'] = $this->attributeService->render($token);

        $response =  new JsonResponse($data, Response::HTTP_OK, []);
        $response->headers->set('Access-Control-Allow-Methods', 'GET');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }

    /**
     * @Route("/{collectionIdentifier}", methods={"GET"})
     *
     * @OA\Response(
     *     response=200,
     *     description="Return tokens by collection"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Not found"
     * )
     * @OA\Parameter(
     *     in="path", name="collectionIdentifier", description="Collection identifier", @OA\Schema(type="string")
     * )
     * @OA\Parameter(
     *     in="query", name="limit", description="Limit per page", @OA\Schema(type="int")
     * )
     * @OA\Parameter(
     *     in="query", name="offset", description="Offset", @OA\Schema(type="int")
     * )
     * @OA\Tag(name="Tokens")
     */
    public function cGet($collectionIdentifier, Request $request)
    {
        $tokens = $this->tokenRepository->findByCollectionIdentifier(
            $collectionIdentifier,
            $request->query->get('limit', 50),
            $request->query->get('offset', 1)
        );
        if (null === $tokens) {
            throw new JsonException(
                sprintf('Tokens from identifier collection %s not found', $collectionIdentifier),
                Response::HTTP_NOT_FOUND
            );
        }

        $hateoas = HateoasBuilder::create()->build();
        $content = $hateoas->serialize($tokens, 'json');

        $response =  new JsonResponse($content, Response::HTTP_OK, [], true);
        $response->headers->set('Access-Control-Allow-Methods', 'GET');
        $response->headers->set('Access-Control-Allow-Origin', '*');

        return $response;
    }
}
