<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Collection\OfferAttributePatchOperationCollection;
use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Collection\OfferPriceUpdateCollection;
use malpka32\InPostBuySdk\Collection\OfferStockUpdateCollection;
use malpka32\InPostBuySdk\Dto\Common\ListSort;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferAttributePatchOperationDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferPriceUpdateDto;
use malpka32\InPostBuySdk\Dto\Offer\Command\OfferStockUpdateDto;
use malpka32\InPostBuySdk\Dto\Offer\Core\MoneyDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferEventType;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
use malpka32\InPostBuySdk\Dto\Offer\OfferStatus;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferDetailsDto;
use malpka32\InPostBuySdk\Dto\Offer\Response\OfferPutResultDto;
use malpka32\InPostBuySdk\Dto\Offer\PriceDto;
use malpka32\InPostBuySdk\Dto\Offer\Product\ProductDto;
use malpka32\InPostBuySdk\Dto\Offer\StockDto;
use malpka32\InPostBuySdk\Mapper\Offer\Deposit\DepositLabelMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferCollectionMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferDtoMapper;
use malpka32\InPostBuySdk\Repository\OffersRepository;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use malpka32\InPostBuySdk\Tests\Fixtures\FakeOffersEndpoint;
use PHPUnit\Framework\TestCase;

final class OffersRepositoryTest extends TestCase
{
    public function testGetOffersReturnsMappedCollection(): void
    {
        $data = ApiMocks::offersListResponse();
        $endpoint = new FakeOffersEndpoint(listResponse: $data);
        $repository = $this->createRepository($endpoint);

        $result = $repository->getOffers();

        $this->assertCount(1, $result);
        $this->assertSame('Test Product', $result->offsetGet(0)->product->name);
    }

    public function testGetOffersPassesSortEnumsToEndpoint(): void
    {
        $endpoint = new FakeOffersEndpoint(listResponse: ApiMocks::offersListResponse());
        $repository = $this->createRepository($endpoint);

        $repository->getOffers(
            offerStatus: [OfferStatus::PENDING, OfferStatus::PUBLISHED],
            limit: 20,
            offset: 10,
            sort: [ListSort::UPDATED_AT_DESC, ListSort::STATUS_ASC],
        );

        $this->assertNotNull($endpoint->lastListCall);
        $this->assertSame([OfferStatus::PENDING, OfferStatus::PUBLISHED], $endpoint->lastListCall['offerStatus']);
        $this->assertSame(20, $endpoint->lastListCall['limit']);
        $this->assertSame(10, $endpoint->lastListCall['offset']);
        $this->assertSame([ListSort::UPDATED_AT_DESC, ListSort::STATUS_ASC], $endpoint->lastListCall['sort']);
    }

    public function testPutOfferCreateReturnsResultDto(): void
    {
        $created = ApiMocks::offerCreatedResponse();
        $endpoint = new FakeOffersEndpoint(createResponse: $created);
        $repository = $this->createRepository($endpoint);
        $dto = $this->createMinimalOfferDto('NEW-SKU');

        $result = $repository->putOffer($dto);

        $this->assertInstanceOf(OfferPutResultDto::class, $result);
        $this->assertSame('cmd-uuid-123', $result->commandId);
        $this->assertSame('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $result->offerId);
        $this->assertSame('SKU-001', $result->externalId);
    }

    public function testPutOfferUpdateCallsUpdateEndpoint(): void
    {
        $updateResponse = ['metadata' => null, 'offer' => ApiMocks::singleOfferPayload()];
        $endpoint = new FakeOffersEndpoint(createResponse: $updateResponse);
        $repository = $this->createRepository($endpoint);
        $dto = $this->createMinimalOfferDto('UPD-SKU', inpostOfferId: 'existing-id');

        $result = $repository->putOffer($dto);

        $this->assertInstanceOf(OfferDetailsDto::class, $result);
        $this->assertSame('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $result->offer->inpostOfferId);
        $this->assertSame('SKU-001', $result->offer->externalId);
    }

    public function testPutOffersEmptyReturnsEmptyCollection(): void
    {
        $endpoint = new FakeOffersEndpoint();
        $repository = $this->createRepository($endpoint);

        $result = $repository->putOffers(new OfferCollection());

        $this->assertCount(0, $result);
    }

    public function testPutOffersBatchReturnsIds(): void
    {
        $batchResponse = ApiMocks::batchOffersCreatedResponse();
        $endpoint = new FakeOffersEndpoint(createBatchResponse: $batchResponse);
        $repository = $this->createRepository($endpoint);

        $offers = new OfferCollection();
        $offers->add($this->createMinimalOfferDto('A'));
        $offers->add($this->createMinimalOfferDto('B'));

        $ids = $repository->putOffers($offers);

        $this->assertCount(2, $ids);
        $this->assertSame('offer-uuid-1', $ids->offsetGet(0)->offerId);
        $this->assertSame('offer-uuid-2', $ids->offsetGet(1)->offerId);
    }

    public function testUpdateOfferPricesSendsPayloadAndMapsResults(): void
    {
        $endpoint = new FakeOffersEndpoint();
        $endpoint->commandResultsResponse = [
            ['commandId' => 'cmd-1', 'offerId' => 'offer-1', 'status' => 'PENDING'],
            ['commandId' => 'cmd-2', 'offerId' => 'offer-2', 'status' => 'PENDING'],
        ];
        $repository = $this->createRepository($endpoint);

        $updates = OfferPriceUpdateCollection::fromUpdates(
            new OfferPriceUpdateDto('offer-1', new MoneyDto(9.99, 'PLN')),
            new OfferPriceUpdateDto('offer-2', new MoneyDto(19.99, 'PLN')),
        );

        $result = $repository->updateOfferPrices($updates);

        $this->assertCount(2, $result);
        $this->assertSame('offer-2', $result->offsetGet(1)->offerId);
        $this->assertNotNull($endpoint->lastUpdatePricesPayload);
        $this->assertSame('offer-1', $endpoint->lastUpdatePricesPayload[0]['offerId']);
        $this->assertSame(9.99, $endpoint->lastUpdatePricesPayload[0]['price']['amount']);
    }

    public function testUpdateOfferPricesEmptyDoesNotCallEndpoint(): void
    {
        $endpoint = new FakeOffersEndpoint();
        $repository = $this->createRepository($endpoint);

        $result = $repository->updateOfferPrices(new OfferPriceUpdateCollection());

        $this->assertCount(0, $result);
        $this->assertNull($endpoint->lastUpdatePricesPayload);
    }

    public function testUpdateOfferStocksSendsPayloadAndMapsResults(): void
    {
        $endpoint = new FakeOffersEndpoint();
        $endpoint->commandResultsResponse = [
            ['commandId' => 'cmd-1', 'offerId' => 'offer-1', 'status' => 'PENDING'],
        ];
        $repository = $this->createRepository($endpoint);

        $updates = OfferStockUpdateCollection::fromUpdates(
            new OfferStockUpdateDto('offer-1', new StockDto(50, 'UNIT')),
        );

        $result = $repository->updateOfferStocks($updates);

        $this->assertCount(1, $result);
        $this->assertSame('offer-1', $result->offsetGet(0)->offerId);
        $this->assertNotNull($endpoint->lastUpdateStocksPayload);
        $this->assertSame(50, $endpoint->lastUpdateStocksPayload[0]['stock']['quantity']);
    }

    public function testPatchOfferAttributesWrapsOperationsAndMapsResult(): void
    {
        $endpoint = new FakeOffersEndpoint();
        $endpoint->patchAttributesResponse = ['commandId' => 'cmd-9', 'offerId' => 'offer-9', 'status' => 'PENDING'];
        $repository = $this->createRepository($endpoint);

        $operations = OfferAttributePatchOperationCollection::fromOperations(
            OfferAttributePatchOperationDto::upsert('attr-1', ['Red'], 'pl_PL'),
            OfferAttributePatchOperationDto::remove('attr-2'),
        );

        $result = $repository->patchOfferAttributes('offer-9', $operations);

        $this->assertSame('cmd-9', $result->commandId);
        $this->assertSame('offer-9', $result->offerId);
        $this->assertNotNull($endpoint->lastPatchAttributesCall);
        $this->assertSame('offer-9', $endpoint->lastPatchAttributesCall['offerId']);
        $this->assertArrayHasKey('operations', $endpoint->lastPatchAttributesCall['payload']);
        $this->assertCount(2, $endpoint->lastPatchAttributesCall['payload']['operations']);
        $this->assertSame('UPSERT', $endpoint->lastPatchAttributesCall['payload']['operations'][0]['type']);
        $this->assertSame('REMOVE', $endpoint->lastPatchAttributesCall['payload']['operations'][1]['type']);
    }

    public function testGetOfferEventsAcceptsEnumEventTypes(): void
    {
        $endpoint = new FakeOffersEndpoint();
        $repository = $this->createRepository($endpoint);

        $result = $repository->getOfferEvents(eventType: [OfferEventType::CREATED, OfferEventType::UPDATED], limit: 50);

        $this->assertSame([], $result->getEvents());
    }

    private function createRepository(\malpka32\InPostBuySdk\Api\OffersEndpointInterface $endpoint): OffersRepository
    {
        return new OffersRepository(
            $endpoint,
            new OfferCollectionMapper(
                new OfferDtoMapper()
            ),
            new DepositLabelMapper()
        );
    }

    private function createMinimalOfferDto(string $externalId, ?string $inpostOfferId = null): OfferDto
    {
        $product = new ProductDto('Prod', '', 'Brand', 'cat-id', sku: $externalId);
        $stock = new StockDto(0, 'UNIT');
        $price = new PriceDto(0.0, 'PLN', '23%');
        return new OfferDto($externalId, $product, $stock, $price, $inpostOfferId);
    }
}
