<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Mapper;

use malpka32\InPostBuySdk\Mapper\Offer\Core\OfferImageCollectionMapper;
use PHPUnit\Framework\TestCase;

final class OfferImageCollectionMapperTest extends TestCase
{
    public function testMapsImages(): void
    {
        $mapper = new OfferImageCollectionMapper();

        $result = $mapper->map([
            ['fileName' => 'a.png', 'fileUrl' => 'https://cdn/a.png', 'priority' => 1],
            ['fileName' => 'b.png'],
        ]);

        $this->assertNotNull($result);
        $this->assertCount(2, $result);
        $this->assertSame('a.png', $result->offsetGet(0)->fileName);
        $this->assertSame('https://cdn/a.png', $result->offsetGet(0)->fileUrl);
        $this->assertSame(1, $result->offsetGet(0)->priority);
        $this->assertSame('b.png', $result->offsetGet(1)->fileName);
        $this->assertNull($result->offsetGet(1)->fileUrl);
    }

    public function testReturnsNullForNonArrayOrEmpty(): void
    {
        $mapper = new OfferImageCollectionMapper();

        $this->assertNull($mapper->map(null));
        $this->assertNull($mapper->map('nope'));
        $this->assertNull($mapper->map([['noFileName' => 'x']]));
    }
}
