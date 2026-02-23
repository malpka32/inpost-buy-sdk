<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Repository;

use malpka32\InPostBuySdk\Mapper\Attachment\AttachmentMapper;
use malpka32\InPostBuySdk\Repository\OfferAttachmentsRepository;
use malpka32\InPostBuySdk\Tests\Fixtures\FakeOfferAttachmentsEndpoint;
use PHPUnit\Framework\TestCase;

final class OfferAttachmentsRepositoryTest extends TestCase
{
    private OfferAttachmentsRepository $repository;

    protected function setUp(): void
    {
        $this->repository = new OfferAttachmentsRepository(
            new FakeOfferAttachmentsEndpoint(),
            new AttachmentMapper()
        );
    }

    public function testGetAttachmentsReturnsCollection(): void
    {
        $collection = $this->repository->getAttachments('offer-1');
        $this->assertCount(1, $collection);
        $first = $collection->first();
        $this->assertNotNull($first);
        $this->assertSame('att-uuid-123', $first->id);
        $this->assertSame('product.jpg', $first->name);
        $this->assertSame('IMAGE', $first->attachmentType);
    }

    public function testCreateAttachmentReturnsCommandIdAndStatus(): void
    {
        $tmpFile = new \SplFileInfo(tempnam(sys_get_temp_dir(), 'inpost_'));
        $result = $this->repository->createAttachment('offer-1', 'IMAGE', $tmpFile);
        @unlink($tmpFile->getPathname());

        $this->assertSame('cmd-att-1', $result->commandId);
        $this->assertSame('PENDING', $result->status);
    }

    public function testDownloadAttachmentReturnsResponse(): void
    {
        $response = new class () implements \Symfony\Contracts\HttpClient\ResponseInterface {
            public function getStatusCode(): int
            {
                return 200;
            }

            public function getHeaders(bool $throw = true): array
            {
                return [];
            }

            public function getContent(bool $throw = true): string
            {
                return 'binary-content';
            }

            public function toArray(bool $throw = true): array
            {
                return [];
            }

            public function cancel(): void
            {
            }

            public function getInfo(?string $type = null): mixed
            {
                return null;
            }
        };

        $fakeEndpoint = new FakeOfferAttachmentsEndpoint();
        $fakeEndpoint->setDownloadResponse($response);
        $repository = new OfferAttachmentsRepository($fakeEndpoint, new AttachmentMapper());

        $result = $repository->downloadAttachment('offer-1', 'att-1');
        $this->assertSame('binary-content', $result->getContent(false));
    }

    public function testDeleteAttachmentSucceeds(): void
    {
        $this->repository->deleteAttachment('offer-1', 'att-1');
        $this->expectNotToPerformAssertions();
    }
}
