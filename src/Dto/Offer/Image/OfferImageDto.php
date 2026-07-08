<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Image;

/**
 * Offer image (OpenAPI: Images).
 *
 * fileName – image file name, fileUrl – public URL, priority – display order.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class OfferImageDto
{
    public function __construct(
        /** Nazwa pliku obrazu. */
        public string $fileName,
        /** Publiczny URL obrazu. */
        public ?string $fileUrl = null,
        /** Kolejność wyświetlania. */
        public ?int $priority = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $payload = ['fileName' => $this->fileName];
        if ($this->fileUrl !== null && $this->fileUrl !== '') {
            $payload['fileUrl'] = $this->fileUrl;
        }
        if ($this->priority !== null) {
            $payload['priority'] = $this->priority;
        }

        return $payload;
    }
}
