<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Api;

use malpka32\InPostBuySdk\Transport\ApiTransport;
use malpka32\InPostBuySdk\Transport\ResponseDecoder;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Offer Attachments endpoint – list, create, download, delete.
 *
 * @see https://inpsa-api-portal.inpost-group.com/gokart-api.html
 */
final class OfferAttachmentsEndpoint implements OfferAttachmentsEndpointInterface
{
    private const ORGANIZATION_OFFERS_PATH = '/v1/organizations/%s/offers/%s/attachments';

    public function __construct(
        private readonly ApiTransport $transport,
        private readonly ResponseDecoder $responseDecoder,
        private readonly string $baseUrl,
        private readonly string $organizationId,
    ) {
    }

    private function attachmentsPath(string $offerId, ?string $attachmentId = null): string
    {
        $path = sprintf(
            self::ORGANIZATION_OFFERS_PATH,
            rawurlencode($this->organizationId),
            rawurlencode($offerId)
        );
        return $attachmentId !== null
            ? $path . '/' . rawurlencode($attachmentId)
            : $path;
    }

    /**
     * List attachments for an offer.
     *
     * @return array<string, mixed> { page: { limit, offset, total }, data: Attachment[] }
     */
    public function list(string $offerId, ?int $limit = null, ?int $offset = null): array
    {
        $params = array_filter([
            'limit' => $limit,
            'offset' => $offset,
        ], fn ($v) => $v !== null);

        $url = $this->baseUrl . $this->attachmentsPath($offerId);
        if ($params !== []) {
            $url .= '?' . http_build_query($params);
        }

        $response = $this->transport->request('GET', $url);
        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Create attachment (multipart upload).
     *
     * @param \SplFileInfo $file File SplFileInfo
     * @return array<string, mixed> { commandId, status }
     */
    public function create(string $offerId, string $attachmentType, mixed $file): array
    {
        $url = $this->baseUrl . $this->attachmentsPath($offerId) . '?' . http_build_query(['attachmentType' => $attachmentType]);

        if (is_resource($file)) {
            throw new \InvalidArgumentException('File must be SplFileInfo, resource is not supported for multipart upload');
        }
        if (!$file instanceof \SplFileInfo) {
            throw new \InvalidArgumentException('File must be SplFileInfo');
        }
        $filePart = DataPart::fromPath($file->getPathname(), $file->getFilename());

        $formData = new FormDataPart(['file' => $filePart]);

        $response = $this->transport->requestWithOptions('POST', $url, [
            'headers' => array_merge(
                $formData->getPreparedHeaders()->toArray(),
                ['Accept-Language' => 'pl']
            ),
            'body' => $formData->bodyToIterable(),
        ]);

        return $this->responseDecoder->decodeToArray($response);
    }

    /**
     * Download attachment (returns raw response for binary content).
     */
    public function download(string $offerId, string $attachmentId): ResponseInterface
    {
        $url = $this->baseUrl . $this->attachmentsPath($offerId, $attachmentId);
        return $this->transport->request('GET', $url);
    }

    /**
     * Delete attachment.
     */
    public function delete(string $offerId, string $attachmentId): void
    {
        $url = $this->baseUrl . $this->attachmentsPath($offerId, $attachmentId);
        $this->transport->request('DELETE', $url);
    }
}
