<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer\Attachment;

/**
 * Offer attachment (OpenAPI: Attachment).
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#operation/getOfferAttachmentsV1
 */
final class AttachmentDto
{
    public function __construct(
        public string $id,
        public string $name,
        /** AttachmentType: IMAGE, VIDEO, AUDIO, WARRANTY_CARD, MANUAL, etc. */
        public string $attachmentType,
        public string $createdAt,
        public string $url,
    ) {
    }
}
