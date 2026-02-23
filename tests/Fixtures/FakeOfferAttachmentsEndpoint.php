<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Fixtures;

use malpka32\InPostBuySdk\Api\OfferAttachmentsEndpointInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Test double for OfferAttachmentsEndpoint.
 */
final class FakeOfferAttachmentsEndpoint implements OfferAttachmentsEndpointInterface
{
    /** @var array<string, mixed> */
    private array $listResponse;

    /** @var ResponseInterface|null */
    private ?ResponseInterface $downloadResponse = null;

    /**
     * @param array<string, mixed> $listResponse
     */
    public function __construct(array $listResponse = [])
    {
        $this->listResponse = $listResponse ?: ApiMocks::attachmentsListResponse();
    }

    public function setDownloadResponse(ResponseInterface $response): void
    {
        $this->downloadResponse = $response;
    }

    public function list(string $offerId, ?int $limit = null, ?int $offset = null): array
    {
        return $this->listResponse;
    }

    public function create(string $offerId, string $attachmentType, mixed $file): array
    {
        return ['commandId' => 'cmd-att-1', 'status' => 'PENDING'];
    }

    public function download(string $offerId, string $attachmentId): ResponseInterface
    {
        if ($this->downloadResponse !== null) {
            return $this->downloadResponse;
        }
        throw new \RuntimeException('FakeOfferAttachmentsEndpoint::download() – call setDownloadResponse() with a stub first');
    }

    public function delete(string $offerId, string $attachmentId): void
    {
    }
}
