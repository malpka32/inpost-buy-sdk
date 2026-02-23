<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Mapper\Category\CategoryCollectionMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class CategoryResponseMapperTest extends TestCase
{
    private CategoryCollectionMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new CategoryCollectionMapper();
    }

    public function testMapEmptyResponseReturnsEmptyCollection(): void
    {
        $result = $this->mapper->map([]);
        $this->assertCount(0, $result);
    }

    /** API response: {"categories": [{"id","name","parentId","parent_id"}, ...]}. */
    public function testMapCategoriesFromOpenApiResponse(): void
    {
        $data = ['categories' => ApiMocks::categoriesResponse()];
        $result = $this->mapper->map($data);

        $this->assertCount(2, $result);
        $first = $result->offsetGet(0);
        $this->assertSame('67909821-cc25-45ec-80ce-5ac4f2f01032', $first->id);
        $this->assertSame('Consumer Electronics', $first->name);
        $this->assertNull($first->parentId);
    }

    public function testMapCategoriesFromItemsKey(): void
    {
        $data = [
            'items' => [
                ['id' => 'cat-1', 'name' => 'Kategoria A', 'parentId' => null],
                ['id' => 'cat-2', 'name' => 'Kategoria B', 'parentId' => 'cat-1'],
            ],
        ];
        $result = $this->mapper->map($data);

        $this->assertCount(2, $result);
        $this->assertSame('cat-1', $result->offsetGet(0)->id);
        $this->assertSame('cat-2', $result->offsetGet(1)->id);
        $this->assertSame('cat-1', $result->offsetGet(1)->parentId);
    }

    public function testMapCategoriesFromCategoriesKeyWithParentId(): void
    {
        $data = ApiMocks::categoriesResponseItemsWithParentId();
        $result = $this->mapper->map($data);

        $this->assertCount(2, $result);
        $this->assertSame('root-1', $result->offsetGet(0)->id);
        $this->assertSame('Root', $result->offsetGet(0)->name);
        $this->assertNull($result->offsetGet(0)->parentId);
        $this->assertSame('child-1', $result->offsetGet(1)->id);
        $this->assertSame('Child', $result->offsetGet(1)->name);
        $this->assertSame('root-1', $result->offsetGet(1)->parentId);
    }

    /** Flat list – API returns categories as flat array, parent_id/parentId per item. */
    public function testMapFlatCategoriesWithParentId(): void
    {
        $data = [
            'categories' => [
                ['id' => 'c1', 'name' => 'Dla dzieci', 'parentId' => null, 'parent_id' => null],
                ['id' => 'c2', 'name' => 'Zabawki', 'parentId' => 'c1', 'parent_id' => 'c1'],
            ],
        ];
        $result = $this->mapper->map($data);

        $this->assertCount(2, $result);
        $this->assertSame('c1', $result->offsetGet(0)->id);
        $this->assertSame('Dla dzieci', $result->offsetGet(0)->name);
        $this->assertNull($result->offsetGet(0)->parentId);
        $this->assertSame('c2', $result->offsetGet(1)->id);
        $this->assertSame('Zabawki', $result->offsetGet(1)->name);
        $this->assertSame('c1', $result->offsetGet(1)->parentId);
    }
}
