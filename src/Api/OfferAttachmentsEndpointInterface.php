<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use Symfony\Contracts\HttpClient\ResponseInterface;

interface OfferAttachmentsEndpointInterface
{
    /**
     * @return array<string, mixed>
     */
    public function list(string $offerId, ?int $limit = null, ?int $offset = null): array;

    /**
     * @param resource|\SplFileInfo $file
     * @return array<string, mixed>
     */
    public function create(string $offerId, string $attachmentType, mixed $file): array;

    public function download(string $offerId, string $attachmentId): ResponseInterface;

    public function delete(string $offerId, string $attachmentId): void;
}
