<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Api\CategoriesEndpoint;
use malpka32\InPostBuySdk\Collection\CategoryCollection;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferIdCollection;
use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Api\OffersEndpoint;
use malpka32\InPostBuySdk\Api\OrdersEndpoint;
use malpka32\InPostBuySdk\Auth\ClientCredentialsTokenProvider;
use malpka32\InPostBuySdk\Config\InPostBuyEndpoints;
use malpka32\InPostBuySdk\Dto\CategoryDto;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\OrderDto;
use malpka32\InPostBuySdk\Dto\OrderStatusDto;
use malpka32\InPostBuySdk\Exception\ApiException;
use malpka32\InPostBuySdk\Mapper\AttributeValueMapper;
use malpka32\InPostBuySdk\Mapper\CategoryResponseMapper;
use malpka32\InPostBuySdk\Mapper\DimensionMapper;
use malpka32\InPostBuySdk\Mapper\OfferResponseMapper;
use malpka32\InPostBuySdk\Mapper\OrderResponseMapper;
use malpka32\InPostBuySdk\Repository\CategoriesRepository;
use malpka32\InPostBuySdk\Repository\OffersRepository;
use malpka32\InPostBuySdk\Repository\OrdersRepository;
use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * InPost Buy REST API client (OpenAPI 1.3.1).
 * Thin facade over repositories.
 */
final class InPostBuyClient implements InPostBuyClientInterface
{
    private readonly CategoriesRepository $categoriesRepository;
    private readonly OffersRepository $offersRepository;
    private readonly OrdersRepository $ordersRepository;

    public function __construct(
        HttpClientInterface $httpClient,
        string $clientId,
        string $clientSecret,
        string $organizationId,
        bool $sandbox = false,
    ) {
        $baseUrl = InPostBuyEndpoints::baseUrl($sandbox);
        $tokenUrl = InPostBuyEndpoints::tokenUrl($sandbox);

        $tokenProvider = new ClientCredentialsTokenProvider($httpClient, $tokenUrl, $clientId, $clientSecret);
        $transport = new ApiTransport($httpClient, $tokenProvider);
        $responseDecoder = new ResponseDecoder();

        $categoriesEndpoint = new CategoriesEndpoint($transport, $responseDecoder, $baseUrl);
        $offersEndpoint = new OffersEndpoint($transport, $responseDecoder, $baseUrl, $organizationId);
        $ordersEndpoint = new OrdersEndpoint($transport, $responseDecoder, $baseUrl, $organizationId);

        $this->categoriesRepository = new CategoriesRepository($categoriesEndpoint, new CategoryResponseMapper());
        $this->offersRepository = new OffersRepository(
            $offersEndpoint,
            new OfferResponseMapper(
                new DimensionMapper(),
                new AttributeValueMapper()
            )
        );
        $this->ordersRepository = new OrdersRepository($ordersEndpoint, new OrderResponseMapper());
    }

    public function getCategories(): CategoryCollection
    {
        return $this->categoriesRepository->getCategories();
    }

    public function putCategory(CategoryDto $dto): string
    {
        throw new ApiException('InPost Buy API does not support category create/update – read-only tree.');
    }

    public function getOffers(
        ?array $offerStatus = null,
        ?int $limit = null,
        ?int $offset = null,
        ?array $sort = null
    ): OfferCollection {
        return $this->offersRepository->getOffers($offerStatus, $limit, $offset, $sort);
    }

    public function putOffer(OfferDto $dto): string
    {
        return $this->offersRepository->putOffer($dto);
    }

    public function putOffers(OfferCollection $offers): OfferIdCollection
    {
        return $this->offersRepository->putOffers($offers);
    }

    public function getOrders(?string $status = null): OrderCollection
    {
        return $this->ordersRepository->getOrders($status);
    }

    public function getOrder(string $inpostOrderId): ?OrderDto
    {
        return $this->ordersRepository->getOrder($inpostOrderId);
    }

    public function updateOrderStatus(string $inpostOrderId, OrderStatusDto $status): void
    {
        $this->ordersRepository->updateOrderStatus($inpostOrderId, $status);
    }
}
