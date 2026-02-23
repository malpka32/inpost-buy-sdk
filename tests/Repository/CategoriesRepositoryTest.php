<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Mapper\Attribute\AttributeDefinitionMapper;
use malpka32\InPostBuySdk\Mapper\Category\CategoryDetailedMapper;
use malpka32\InPostBuySdk\Mapper\Category\CategoryTreeStructureMapper;
use malpka32\InPostBuySdk\Repository\CategoriesRepository;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use malpka32\InPostBuySdk\Tests\Fixtures\FakeCategoriesEndpoint;
use PHPUnit\Framework\TestCase;

final class CategoriesRepositoryTest extends TestCase
{
    private function createRepository(mixed $data): CategoriesRepository
    {
        $endpoint = new FakeCategoriesEndpoint($data);

        return new CategoriesRepository(
            $endpoint,
            new CategoryTreeStructureMapper(),
            new CategoryDetailedMapper(),
            new AttributeDefinitionMapper()
        );
    }

    /**
     * InPost API returns tree format: [{"id","name","leaf","children":[...]}, ...]
     */
    public function testGetCategoriesReturnsTreeFromApiFormat(): void
    {
        $data = ApiMocks::categoriesTreeResponse();
        $repository = $this->createRepository($data);

        $tree = $repository->getCategories();

        $this->assertCount(1, $tree);
        $root = $tree->offsetGet(0);
        $this->assertSame('root-uuid', $root->id);
        $this->assertSame('Electronics', $root->name);
        $this->assertCount(1, $root->children);
        $child = $root->children[0];
        $this->assertSame('child-uuid', $child->id);
        $this->assertSame('Phones', $child->name);
        $this->assertSame('root-uuid', $child->parentId);
    }

    public function testGetCategoriesReturnsEmptyForEmptyResponse(): void
    {
        $repository = $this->createRepository([]);

        $tree = $repository->getCategories();

        $this->assertCount(0, $tree);
    }
}
