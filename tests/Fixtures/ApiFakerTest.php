<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use malpka32\InPostBuySdk\Mapper\Category\CategoryCollectionMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferCollectionMapper;
use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferDtoMapper;
use malpka32\InPostBuySdk\Mapper\Order\OrderCollectionMapper;
use PHPUnit\Framework\TestCase;

/**
 * Tests that ApiFaker generates valid data – mapping passes for many random sets.
 */
final class ApiFakerTest extends TestCase
{
    private ApiFaker $faker;

    protected function setUp(): void
    {
        $this->faker = new ApiFaker();
    }

    public function testCategoriesMapToCategoryCollection(): void
    {
        $mapper = new CategoryCollectionMapper();
        for ($i = 0; $i < 5; $i++) {
            $data = $this->faker->categoriesResponse('categories', 2);
            $result = $mapper->map($data);
            $this->assertCount(2, $result);
            $this->assertNotNull($result->offsetGet(0)->id);
            $this->assertNotNull($result->offsetGet(0)->name);
        }
    }

    public function testOffersListMapsToOfferCollection(): void
    {
        $mapper = new OfferCollectionMapper(
            new OfferDtoMapper()
        );
        for ($i = 0; $i < 3; $i++) {
            $data = $this->faker->offersList(2);
            $result = $mapper->map($data);
            $this->assertCount(2, $result);
        }
    }

    public function testOrdersListMapsToOrderCollection(): void
    {
        $mapper = new OrderCollectionMapper();
        for ($i = 0; $i < 3; $i++) {
            $data = $this->faker->ordersList(2, 'items');
            $result = $mapper->map($data);
            $this->assertCount(2, $result);
            $this->assertNotNull($result->offsetGet(0)->createdAt);
        }
    }

    public function testSingleOfferHasValidStructure(): void
    {
        $offer = $this->faker->singleOffer();
        $this->assertArrayHasKey('id', $offer);
        $this->assertArrayHasKey('product', $offer);
        $this->assertArrayHasKey('stock', $offer);
        $this->assertArrayHasKey('price', $offer);
        $this->assertIsArray($offer['product']);
        $this->assertSame('UNIT', $offer['stock']['unit']);
    }

    public function testErrorResponseHasErrorCode(): void
    {
        $err = $this->faker->errorResponse();
        $this->assertArrayHasKey('errorCode', $err);
        $this->assertNotEmpty($err['errorCode']);
    }

    public function testDimensionHasAllKeys(): void
    {
        $dim = $this->faker->dimension();
        $this->assertArrayHasKey('width', $dim);
        $this->assertArrayHasKey('height', $dim);
        $this->assertArrayHasKey('length', $dim);
        $this->assertArrayHasKey('weight', $dim);
    }

    public function testBatchOffersCreatedReturnsListOfIds(): void
    {
        $batch = $this->faker->batchOffersCreated(4);
        $this->assertCount(4, $batch);
        foreach ($batch as $item) {
            $this->assertArrayHasKey('offerId', $item);
        }
    }
}
