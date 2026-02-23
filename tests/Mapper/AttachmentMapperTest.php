<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Mapper;

use malpka32\InPostBuySdk\Mapper\Attachment\AttachmentMapper;
use malpka32\InPostBuySdk\Tests\Fixtures\ApiMocks;
use PHPUnit\Framework\TestCase;

final class AttachmentMapperTest extends TestCase
{
    private AttachmentMapper $mapper;

    protected function setUp(): void
    {
        $this->mapper = new AttachmentMapper();
    }

    public function testMapReturnsCollection(): void
    {
        $data = ApiMocks::attachmentsListResponse();
        $collection = $this->mapper->map($data);
        $this->assertCount(1, $collection);
        $first = $collection->first();
        $this->assertNotNull($first);
        $this->assertSame('att-uuid-123', $first->id);
        $this->assertSame('product.jpg', $first->name);
        $this->assertSame('IMAGE', $first->attachmentType);
        $this->assertSame('2025-02-15T13:45:30', $first->createdAt);
        $this->assertStringContainsString('product.jpg', $first->url);
    }

    public function testMapEmptyDataReturnsEmptyCollection(): void
    {
        $collection = $this->mapper->map(['data' => []]);
        $this->assertCount(0, $collection);
    }
}
