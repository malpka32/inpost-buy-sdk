<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Mapper;

use malpka32\InPostBuySdk\Mapper\Offer\Command\OfferCommandResultCollectionMapper;
use PHPUnit\Framework\TestCase;

final class OfferCommandResultCollectionMapperTest extends TestCase
{
    public function testMapsPlainList(): void
    {
        $mapper = new OfferCommandResultCollectionMapper();

        $result = $mapper->map([
            ['commandId' => 'cmd-1', 'offerId' => 'offer-1', 'status' => 'PENDING'],
            ['commandId' => 'cmd-2', 'offerId' => 'offer-2', 'status' => 'PENDING'],
        ]);

        $this->assertCount(2, $result);
        $this->assertSame('offer-1', $result->offsetGet(0)->offerId);
        $this->assertSame('cmd-2', $result->offsetGet(1)->commandId);
    }

    public function testMapsDataWrapper(): void
    {
        $mapper = new OfferCommandResultCollectionMapper();

        $result = $mapper->map(['data' => [
            ['commandId' => 'cmd-3', 'offerId' => 'offer-3', 'status' => 'PENDING'],
        ]]);

        $this->assertCount(1, $result);
        $this->assertSame('offer-3', $result->offsetGet(0)->offerId);
    }

    public function testMapsEmptyOnInvalidData(): void
    {
        $mapper = new OfferCommandResultCollectionMapper();

        $this->assertCount(0, $mapper->map([]));
        $this->assertCount(0, $mapper->map(['data' => 'nope']));
    }
}
