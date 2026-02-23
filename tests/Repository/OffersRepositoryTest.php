<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Collection\OfferCollection;
use malpka32\InPostBuySdk\Dto\Offer\OfferDto;
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
