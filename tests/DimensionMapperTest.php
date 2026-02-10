<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Mapper\DimensionMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class DimensionMapperTest extends TestCase
{
    private DimensionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new DimensionMapper();
    }

    public function testMapNullReturnsNull(): void
    {
        $this->assertNull($this->mapper->map(null));
    }

    public function testMapIncompleteDataReturnsNull(): void
    {
        $this->assertNull($this->mapper->map([]));
        $this->assertNull($this->mapper->map(['width' => 1]));
    }

    public function testMapReturnsDimensionDtoFromOpenApiPayload(): void
    {
        $data = ApiMocks::dimensionPayload();
        $dto = $this->mapper->map($data);

        $this->assertNotNull($dto);
        $this->assertSame(200, $dto->width);
        $this->assertSame(100, $dto->height);
        $this->assertSame(300, $dto->length);
        $this->assertSame(340, $dto->weight);
    }

    public function testMapCastsToInt(): void
    {
        $data = [
            'width' => '100',
            'height' => 200,
            'length' => 300,
            'weight' => 400,
        ];
        $dto = $this->mapper->map($data);
        $this->assertSame(100, $dto->width);
        $this->assertSame(200, $dto->height);
    }
}
