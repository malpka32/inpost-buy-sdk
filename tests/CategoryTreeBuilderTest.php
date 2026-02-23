<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests;

use malpka32\InPostBuySdk\Dto\Category\CategoryDto;
use malpka32\InPostBuySdk\Builder\CategoryTreeBuilder;
use PHPUnit\Framework\TestCase;

final class CategoryTreeBuilderTest extends TestCase
{
    private CategoryTreeBuilder $builder;

    protected function setUp(): void
    {
        $this->builder = new CategoryTreeBuilder();
    }

    public function testBuildEmptyCollectionReturnsEmptyRoots(): void
    {
        $flat = new \malpka32\InPostBuySdk\Collection\CategoryCollection();
        $tree = $this->builder->build($flat);
        $this->assertCount(0, $tree);
    }

    public function testBuildFlatListWithNoParentsReturnsAllAsRoots(): void
    {
        $flat = new \malpka32\InPostBuySdk\Collection\CategoryCollection([
            new CategoryDto('a', 'A', null),
            new CategoryDto('b', 'B', null),
        ]);
        $tree = $this->builder->build($flat);
        $this->assertCount(2, $tree);
        $this->assertSame('a', $tree->offsetGet(0)->id);
        $this->assertSame('b', $tree->offsetGet(1)->id);
        $this->assertCount(0, $tree->offsetGet(0)->children);
        $this->assertCount(0, $tree->offsetGet(1)->children);
    }

    public function testBuildParentAndChildReturnsOneRootWithOneChild(): void
    {
        $flat = new \malpka32\InPostBuySdk\Collection\CategoryCollection([
            new CategoryDto('root', 'Root', null),
            new CategoryDto('child', 'Child', 'root'),
        ]);
        $tree = $this->builder->build($flat);
        $this->assertCount(1, $tree);
        $root = $tree->offsetGet(0);
        $this->assertSame('root', $root->id);
        $this->assertSame('Root', $root->name);
        $this->assertNull($root->parentId);
        $this->assertCount(1, $root->children);
        $this->assertSame('child', $root->children[0]->id);
        $this->assertSame('Child', $root->children[0]->name);
        $this->assertSame('root', $root->children[0]->parentId);
        $this->assertCount(0, $root->children[0]->children);
    }

    public function testBuildTwoRootsWithChildren(): void
    {
        $flat = new \malpka32\InPostBuySdk\Collection\CategoryCollection([
            new CategoryDto('r1', 'R1', null),
            new CategoryDto('r2', 'R2', null),
            new CategoryDto('c1', 'C1', 'r1'),
            new CategoryDto('c2', 'C2', 'r2'),
        ]);
        $tree = $this->builder->build($flat);
        $this->assertCount(2, $tree);
        $this->assertSame('r1', $tree->offsetGet(0)->id);
        $this->assertCount(1, $tree->offsetGet(0)->children);
        $this->assertSame('c1', $tree->offsetGet(0)->children[0]->id);
        $this->assertSame('r2', $tree->offsetGet(1)->id);
        $this->assertCount(1, $tree->offsetGet(1)->children);
        $this->assertSame('c2', $tree->offsetGet(1)->children[0]->id);
    }

    public function testCollectionToTreeReturnsSameStructure(): void
    {
        $flat = new \malpka32\InPostBuySdk\Collection\CategoryCollection([
            new CategoryDto('root', 'Root', null),
            new CategoryDto('child', 'Child', 'root'),
        ]);
        $tree = $flat->toTree();
        $this->assertCount(1, $tree);
        $this->assertSame('root', $tree->offsetGet(0)->id);
        $this->assertCount(1, $tree->offsetGet(0)->children);
        $this->assertSame('child', $tree->offsetGet(0)->children[0]->id);
    }
}
