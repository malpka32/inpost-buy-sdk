<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Client;

use malpka32\InPostBuySdk\Api\CategoriesEndpoint;
use malpka32\InPostBuySdk\Api\OfferAttachmentsEndpoint;
use malpka32\InPostBuySdk\Collection\AttachmentCollection;
use malpka32\InPostBuySdk\Collection\AttributeDefinitionCollection;
use malpka32\InPostBuySdk\Collection\CategoryTreeCollection;
use malpka32\InPostBuySdk\Collection\DepositLabelCollection;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferPutResultCollection;
use malpka32\InPostBuySdk\Collection\OrderCollection;
use malpka32\InPostBuySdk\Api\OffersEndpoint;
use malpka32\InPostBuySdk\Api\OrdersEndpoint;
use malpka32\InPostBuySdk\Auth\AccessTokenProviderInterface;
use malpka32\InPostBuySdk\Auth\ClientCredentialsTokenProvider;
use malpka32\InPostBuySdk\Config\InPostBuyEndpoints;
use malpka32\InPostBuySdk\Config\Language;
use malpka32\InPostBuySdk\Dto\Category\CategoryDetailedDto;
use malpka32\InPostBuySdk\Dto\Category\CategoryDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\CommandStatusDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferDetailsDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferEventsResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferHintResultDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferPutResultDto;
use malpka32\InPostBuySdk\Dto\Order\OrderDto;
use malpka32\InPostBuySdk\Dto\Order\OrderStatusDto;
use malpka32\InPostBuySdk\Exception\ApiException;
use malpka32\InPostBuySdk\Mapper\Attribute\AttributeDefinitionMapper;
use malpka32\InPostBuySdk\Mapper\Attribute\AttributeValueCollectionMapper;
use malpka32\InPostBuySdk\Mapper\Category\CategoryDetailedMapper;
use malpka32\InPostBuySdk\Mapper\Category\CategoryTreeStructureMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\DimensionMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferCollectionMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferDtoMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferPriceDtoMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferProductDtoMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferStockDtoMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Deposit\OfferDepositPositionDtoMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Gpsr\OfferGpsrSingleMapper;
use malpka32\InPostBuySdk\Mapper\Offer\PostSale\OfferFeaturesSingleMapper;
use malpka32\InPostBuySdk\Mapper\Offer\PostSale\OfferPostSaleSingleMapper;
use malpka32\InPostBuySdk\Mapper\Offer\PostSale\OfferShippingTimeSingleMapper;
use malpka32\InPostBuySdk\Mapper\Order\OrderCollectionMapper;
use malpka32\InPostBuySdk\Repository\CategoriesRepository;
use malpka32\InPostBuySdk\Mapper\Attachment\AttachmentMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Deposit\DepositLabelMapper;
use malpka32\InPostBuySdk\Repository\OfferAttachmentsRepository;
use malpka32\InPostBuySdk\Repository\OffersRepository;
use malpka32\InPostBuySdk\Repository\OrdersRepository;
use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * InPost Buy REST API client (OpenAPI 1.3.1).
 * Thin facade over repositories.
 */
final class InPostBuyClient implements InPostBuyClientInterface
{
    /**
     * Creates client with custom token provider (e.g. PKCE for merchant OAuth).
     */
    public static function createWithTokenProvider(
        HttpClientInterface $httpClient,
        AccessTokenProviderInterface $tokenProvider,
        string $organizationId,
        bool $sandbox = false,
        Language $language = Language::Polish,
    ): self {
        return new self($httpClient, '', '', $organizationId, $sandbox, $tokenProvider, $language);
    }

    private readonly CategoriesRepository $categoriesRepository;
    private readonly OffersRepository $offersRepository;
    private readonly OfferAttachmentsRepository $offerAttachmentsRepository;
    private readonly OrdersRepository $ordersRepository;

    public function __construct(
        HttpClientInterface $httpClient,
        string $clientId,
        string $clientSecret,
        string $organizationId,
        bool $sandbox = false,
        ?AccessTokenProviderInterface $tokenProvider = null,
        Language $language = Language::Polish,
    ) {
        $baseUrl = InPostBuyEndpoints::baseUrl($sandbox);
        $tokenUrl = InPostBuyEndpoints::tokenUrl($sandbox);

        $effectiveTokenProvider = $tokenProvider ?? new ClientCredentialsTokenProvider(
            $httpClient,
            $tokenUrl,
            $clientId,
            $clientSecret
        );
        $transport = new ApiTransport($httpClient, $effectiveTokenProvider, $language);
        $responseDecoder = new ResponseDecoder();

        $categoriesEndpoint = new CategoriesEndpoint($transport, $responseDecoder, $baseUrl);
        $offersEndpoint = new OffersEndpoint($transport, $responseDecoder, $baseUrl, $organizationId);
        $attachmentsEndpoint = new OfferAttachmentsEndpoint($transport, $responseDecoder, $baseUrl, $organizationId);
        $ordersEndpoint = new OrdersEndpoint($transport, $responseDecoder, $baseUrl, $organizationId);

        $this->categoriesRepository = new CategoriesRepository(
            $categoriesEndpoint,
            new CategoryTreeStructureMapper(),
            new CategoryDetailedMapper(),
            new AttributeDefinitionMapper()
        );
        $this->offersRepository = new OffersRepository(
            $offersEndpoint,
            new OfferCollectionMapper(
                new OfferDtoMapper(
                    new OfferProductDtoMapper(
                        new DimensionMapper(),
                        new AttributeValueCollectionMapper()
                    ),
                    new OfferStockDtoMapper(),
                    new OfferPriceDtoMapper(new OfferDepositPositionDtoMapper()),
                    new OfferGpsrSingleMapper(),
                    new OfferShippingTimeSingleMapper(),
                    new OfferPostSaleSingleMapper(),
                    new OfferFeaturesSingleMapper()
                )
            ),
            new DepositLabelMapper()
        );
        $this->offerAttachmentsRepository = new OfferAttachmentsRepository(
            $attachmentsEndpoint,
            new AttachmentMapper()
        );
        $this->ordersRepository = new OrdersRepository($ordersEndpoint, new OrderCollectionMapper());
    }

    /**
     * @param string|null $categoryId Subtree root (null = root)
     * @param int|null    $depth      Depth 0–4
     */
    public function getCategories(?string $categoryId = null, ?int $depth = null): CategoryTreeCollection
    {
        return $this->categoriesRepository->getCategories($categoryId, $depth);
    }

    public function getCategory(string $categoryId, ?int $depth = null): CategoryDetailedDto
    {
        return $this->categoriesRepository->getCategory($categoryId, $depth);
    }

    public function getCategoryAttributes(string $categoryId): AttributeDefinitionCollection
    {
        return $this->categoriesRepository->getCategoryAttributes($categoryId);
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

    /**
     * Create (POST) or update (PATCH) offer. Create → OfferPutResultDto, update → OfferDetailsDto (metadata + offer).
     */
    public function putOffer(OfferDto $dto): OfferPutResultDto|OfferDetailsDto
    {
        return $this->offersRepository->putOffer($dto);
    }

    public function putOffers(OfferCollection $offers): OfferPutResultCollection
    {
        return $this->offersRepository->putOffers($offers);
    }

    public function getOffer(string $offerId): OfferDto
    {
        return $this->offersRepository->getOffer($offerId);
    }

    public function getOfferDetails(string $offerId): OfferDetailsDto
    {
        return $this->offersRepository->getOfferDetails($offerId);
    }

    public function closeOffer(string $offerId): CommandStatusDto
    {
        return $this->offersRepository->closeOffer($offerId);
    }

    public function reopenOffer(string $offerId): CommandStatusDto
    {
        return $this->offersRepository->reopenOffer($offerId);
    }

    public function getOfferCommandStatus(string $commandId): CommandStatusDto
    {
        return $this->offersRepository->getOfferCommandStatus($commandId);
    }

    /**
     * @param list<string>|null $eventType
     */
    public function getOfferEvents(?string $untilId = null, ?array $eventType = null, ?int $limit = null): OfferEventsResultDto
    {
        return $this->offersRepository->getOfferEvents($untilId, $eventType, $limit);
    }

    public function getOfferHint(?string $ean = null, ?string $mpn = null, ?string $name = null, ?int $limit = null, ?int $offset = null): OfferHintResultDto
    {
        return $this->offersRepository->getOfferHint($ean, $mpn, $name, $limit, $offset);
    }

    public function getDepositTypes(): DepositLabelCollection
    {
        return $this->offersRepository->getDepositTypes();
    }

    public function getOfferAttachments(string $offerId, ?int $limit = null, ?int $offset = null): AttachmentCollection
    {
        return $this->offerAttachmentsRepository->getAttachments($offerId, $limit, $offset);
    }

    /**
     * @param resource|\SplFileInfo $file
     */
    public function createOfferAttachment(string $offerId, string $attachmentType, mixed $file): CommandStatusDto
    {
        return $this->offerAttachmentsRepository->createAttachment($offerId, $attachmentType, $file);
    }

    public function downloadOfferAttachment(string $offerId, string $attachmentId): ResponseInterface
    {
        return $this->offerAttachmentsRepository->downloadAttachment($offerId, $attachmentId);
    }

    public function deleteOfferAttachment(string $offerId, string $attachmentId): void
    {
        $this->offerAttachmentsRepository->deleteAttachment($offerId, $attachmentId);
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
