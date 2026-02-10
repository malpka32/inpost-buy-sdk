<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Transport;

use malpka32\InPostBuySdk\Transport\ResponseDecoder;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ResponseDecoderTest extends TestCase
{
    private ResponseDecoder $decoder;

    protected function setUp(): void
    {
        $this->decoder = new ResponseDecoder();
    }

    public function testDecodeToArrayEmptyBodyReturnsEmptyArray(): void
    {
        $response = $this->createMockResponse('');
        $this->assertSame([], $this->decoder->decodeToArray($response));
    }

    public function testDecodeToArrayValidJsonReturnsArray(): void
    {
        $json = '{"categories":[{"id":"x","name":"Test"}]}';
        $response = $this->createMockResponse($json);
        $result = $this->decoder->decodeToArray($response);
        $this->assertIsArray($result);
        $this->assertArrayHasKey('categories', $result);
        $this->assertSame('x', $result['categories'][0]['id']);
    }

    public function testDecodeToArrayInvalidJsonReturnsEmptyArray(): void
    {
        $response = $this->createMockResponse('not json');
        $result = $this->decoder->decodeToArray($response);
        $this->assertSame([], $result);
    }

    private function createMockResponse(string $content): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getContent')->with(false)->willReturn($content);
        return $response;
    }
}
