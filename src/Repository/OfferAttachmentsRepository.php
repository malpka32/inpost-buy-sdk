<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Repository;

use malpka32\InPostBuySdk\Api\OfferAttachmentsEndpointInterface;
use malpka32\InPostBuySdk\Collection\AttachmentCollection;
use malpka32\InPostBuySdk\Dto\Offer\Command\CommandStatusDto;
use malpka32\InPostBuySdk\Mapper\Attachment\AttachmentMapper;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class OfferAttachmentsRepository
{
    public function __construct(
        private readonly OfferAttachmentsEndpointInterface $endpoint,
        private readonly AttachmentMapper $attachmentMapper,
    ) {
    }

    public function getAttachments(string $offerId, ?int $limit = null, ?int $offset = null): AttachmentCollection
    {
        $data = $this->endpoint->list($offerId, $limit, $offset);
        return $this->attachmentMapper->map($data);
    }

    /**
     * @param resource|\SplFileInfo $file
     */
    public function createAttachment(string $offerId, string $attachmentType, mixed $file): CommandStatusDto
    {
        $data = $this->endpoint->create($offerId, $attachmentType, $file);
        return CommandStatusDto::fromArray($data);
    }

    public function downloadAttachment(string $offerId, string $attachmentId): ResponseInterface
    {
        return $this->endpoint->download($offerId, $attachmentId);
    }

    public function deleteAttachment(string $offerId, string $attachmentId): void
    {
        $this->endpoint->delete($offerId, $attachmentId);
    }
}
