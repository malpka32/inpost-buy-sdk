<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Dto\OfferDto;
use malpka32\InPostBuySdk\Dto\PriceDto;
use malpka32\InPostBuySdk\Dto\ProductDto;
use malpka32\InPostBuySdk\Dto\StockDto;
use malpka32\InPostBuySdk\Mapper\AttributeValueMapper;
use malpka32\InPostBuySdk\Mapper\DimensionMapper;
use malpka32\InPostBuySdk\Mapper\OfferResponseMapper;
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

    public function testPutOfferCreateReturnsId(): void
    {
        $created = ApiMocks::offerCreatedResponse();
        $endpoint = new FakeOffersEndpoint(createResponse: $created);
        $repository = $this->createRepository($endpoint);
        $dto = $this->createMinimalOfferDto('NEW-SKU');

        $id = $repository->putOffer($dto);

        $this->assertSame('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $id);
    }

    public function testPutOfferUpdateCallsUpdateEndpoint(): void
    {
        $created = ApiMocks::offerCreatedResponse();
        $endpoint = new FakeOffersEndpoint(createResponse: $created);
        $repository = $this->createRepository($endpoint);
        $dto = $this->createMinimalOfferDto('UPD-SKU', inpostOfferId: 'existing-id');

        $id = $repository->putOffer($dto);

        $this->assertSame('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $id);
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
        $this->assertSame('offer-uuid-1', $ids->offsetGet(0));
        $this->assertSame('offer-uuid-2', $ids->offsetGet(1));
    }

    private function createRepository(\malpka32\InPostBuySdk\Api\OffersEndpointInterface $endpoint): OffersRepository
    {
        return new OffersRepository(
            $endpoint,
            new OfferResponseMapper(new DimensionMapper(), new AttributeValueMapper())
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
