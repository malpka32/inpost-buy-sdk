<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Mapper\CategoryResponseMapper;
use malpka32\InPostBuySdk\Repository\CategoriesRepository;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use malpka32\InPostBuySdk\Tests\Fixtures\FakeCategoriesEndpoint;
use PHPUnit\Framework\TestCase;

final class CategoriesRepositoryTest extends TestCase
{
    public function testGetCategoriesReturnsMappedCollection(): void
    {
        $data = ['categories' => ApiMocks::categoriesResponse()];
        $endpoint = new FakeCategoriesEndpoint($data);
        $repository = new CategoriesRepository($endpoint, new CategoryResponseMapper());

        $result = $repository->getCategories();

        $this->assertCount(2, $result);
        $this->assertSame('Consumer Electronics', $result->offsetGet(0)->name);
    }
}
