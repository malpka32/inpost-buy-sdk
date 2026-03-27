<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Collection;

use malpka32\InPostBuySdk\Collection\OrderEventCollection;
use malpka32\InPostBuySdk\Dto\Order\Response\OrderEventDto;
use PHPUnit\Framework\TestCase;

final class OrderEventCollectionTest extends TestCase
{
    public function testReverseReturnsCollectionWithReversedOrder(): void
    {
        $first = OrderEventDto::fromArray(['id' => 'evt-1']);
        $second = OrderEventDto::fromArray(['id' => 'evt-2']);
        $third = OrderEventDto::fromArray(['id' => 'evt-3']);

        $collection = OrderEventCollection::fromArray([$first, $second, $third]);
        $reversed = $collection->reverse();

        $this->assertCount(3, $reversed);
        $this->assertSame('evt-3', $reversed->offsetGet(0)->id);
        $this->assertSame('evt-2', $reversed->offsetGet(1)->id);
        $this->assertSame('evt-1', $reversed->offsetGet(2)->id);
        $this->assertSame('evt-1', $collection->offsetGet(0)->id);
    }
}
