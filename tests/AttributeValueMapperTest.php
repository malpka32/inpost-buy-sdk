<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Mapper\Attribute\AttributeValueCollectionMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class AttributeValueMapperTest extends TestCase
{
    private AttributeValueCollectionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new AttributeValueCollectionMapper();
    }

    public function testMapEmptySourceReturnsEmptyCollection(): void
    {
        $result = $this->mapper->map([]);
        $this->assertCount(0, $result);
    }

    public function testMapSkipsItemsWithoutIdOrValues(): void
    {
        $data = [
            ['values' => ['x']],       // no id – skipped
            ['id' => 'a'],             // no values – skipped
            ['id' => 'b', 'values' => []],
            ['id' => 'c', 'values' => ['v1']],
        ];
        $result = $this->mapper->map($data);
        $this->assertCount(2, $result);
        $this->assertSame('b', $result->offsetGet(0)->id);
        $this->assertSame([], $result->offsetGet(0)->values);
        $this->assertSame('c', $result->offsetGet(1)->id);
    }

    public function testMapBuildsCollectionFromOpenApiPayload(): void
    {
        $data = [ApiMocks::attributeValuePayload()];
        $result = $this->mapper->map($data);

        $this->assertCount(1, $result);
        $dto = $result->offsetGet(0);
        $this->assertSame('attr-uuid', $dto->id);
        $this->assertSame(['Value1', 'Value2'], $dto->values);
        $this->assertSame('pl', $dto->lang);
    }

    public function testMapFromOfferProductAttributes(): void
    {
        $attributes = [
            ['id' => 'attr-color', 'values' => ['Red'], 'lang' => 'en'],
            ['id' => 'attr-size', 'values' => ['M', 'L']],
        ];
        $result = $this->mapper->map($attributes);

        $this->assertCount(2, $result);
        $this->assertSame('attr-color', $result->offsetGet(0)->id);
        $this->assertSame('attr-size', $result->offsetGet(1)->id);
    }
}
