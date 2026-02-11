<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Mapper\AttributeValueMapper;
use malpka32\InPostBuySdk\Mapper\DimensionMapper;
use malpka32\InPostBuySdk\Mapper\OfferResponseMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class OfferResponseMapperTest extends TestCase
{
    private OfferResponseMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new OfferResponseMapper(
            new DimensionMapper(),
            new AttributeValueMapper()
        );
    }

    public function testMapEmptyResponseReturnsEmptyCollection(): void
    {
        $result = $this->mapper->map([]);
        $this->assertCount(0, $result);
    }

    public function testMapOffersListFromOpenApiPayload(): void
    {
        $data = ApiMocks::offersListResponse();
        $result = $this->mapper->map($data);

        $this->assertCount(1, $result);
        $offer = $result->offsetGet(0);
        $this->assertSame('a1b2c3d4-e5f6-7890-abcd-ef1234567890', $offer->inpostOfferId);
        $this->assertSame('Test Product', $offer->product->name);
        $this->assertSame('SKU-001', $offer->externalId);
        $this->assertSame(99.99, $offer->price->amount);
        $this->assertSame(10, $offer->stock->quantity);
        $this->assertSame('67909821-cc25-45ec-80ce-5ac4f2f01032', $offer->product->categoryId);
        $this->assertNotNull($offer->product->dimension);
        $this->assertSame(200, $offer->product->dimension->width);
        $this->assertCount(2, $offer->product->attributes);
    }

    public function testMapItemMinimalOffer(): void
    {
        $payload = ApiMocks::minimalOfferPayload();
        $dto = $this->mapper->mapItem($payload);

        $this->assertSame('minimal-offer-id', $dto->inpostOfferId);
        $this->assertSame('Minimal', $dto->product->name);
        $this->assertNull($dto->product->dimension);
        $this->assertCount(0, $dto->product->attributes);
    }

    public function testMapUsesDataKeyFirst(): void
    {
        $data = ['data' => [ApiMocks::singleOfferPayload()]];
        $result = $this->mapper->map($data);
        $this->assertCount(1, $result);
    }
}
