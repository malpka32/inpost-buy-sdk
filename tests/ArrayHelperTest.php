<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Mapper\ArrayHelper;
use PHPUnit\Framework\TestCase;

final class ArrayHelperTest extends TestCase
{
    public function testGetReturnsValueBySingleKey(): void
    {
        $data = ['id' => '123', 'name' => 'Test'];
        $this->assertSame('123', ArrayHelper::get($data, 'id'));
        $this->assertSame('Test', ArrayHelper::get($data, 'name'));
    }

    public function testGetReturnsFirstMatchingKeyFromList(): void
    {
        $data = ['offerId' => 'offer-1', 'id' => 'id-1'];
        $this->assertSame('offer-1', ArrayHelper::get($data, ['offerId', 'id']));
    }

    public function testGetReturnsDefaultWhenKeyMissing(): void
    {
        $data = ['a' => 1];
        $this->assertNull(ArrayHelper::get($data, 'b'));
        $this->assertSame(0, ArrayHelper::get($data, 'b', 0));
    }

    public function testGetListReturnsArrayFromFirstMatchingKey(): void
    {
        $data = ['items' => [['id' => 1], ['id' => 2]]];
        $list = ArrayHelper::getList($data, ['items', 'orders']);
        $this->assertCount(2, $list);
        $this->assertSame(1, $list[0]['id']);
    }

    public function testGetListReturnsSingleItemWhenDataHasId(): void
    {
        $data = ['id' => 'single', 'name' => 'One'];
        $list = ArrayHelper::getList($data, ['items', 'categories']);
        $this->assertCount(1, $list);
        $this->assertSame('single', $list[0]['id']);
    }

    public function testGetListFiltersNonArrays(): void
    {
        $data = ['items' => [['id' => 1], null, 'string', ['id' => 2]]];
        $list = ArrayHelper::getList($data, ['items']);
        $this->assertCount(2, $list);
    }

    public function testExtractOfferReturnsOfferKeyWhenPresent(): void
    {
        $item = ['metadata' => [], 'offer' => ['id' => 'offer-1']];
        $this->assertSame('offer-1', ArrayHelper::extractOffer($item)['id']);
    }

    public function testExtractOfferReturnsItemWhenNoOfferKey(): void
    {
        $item = ['id' => 'direct-id'];
        $this->assertSame('direct-id', ArrayHelper::extractOffer($item)['id']);
    }
}
