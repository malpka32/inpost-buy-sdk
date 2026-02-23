<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Dto\Offer;

/**
 * Polityka posprzedażowa (OpenAPI: PostSale).
 *
 * Returns and complaints policy for the offer.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html#tag/Offers/operation/postOffersV1
 */
final class PostSaleDto
{
    public function __construct(
        /** Opis polityki zwrotów (ReturnPolicy.description). */
        public ?string $returnPolicyDescription = null,
        /** Opis polityki reklamacji (ComplaintPolicy.description). */
        public ?string $complaintPolicyDescription = null,
    ) {
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $result = [];
        if (!empty($this->returnPolicyDescription)) {
            $result['returnPolicy'] = ['description' => $this->returnPolicyDescription];
        }
        if (!empty($this->complaintPolicyDescription)) {
            $result['complaintPolicy'] = ['description' => $this->complaintPolicyDescription];
        }
        return $result;
    }
}
