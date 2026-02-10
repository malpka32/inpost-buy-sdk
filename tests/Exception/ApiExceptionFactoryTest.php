<?php

declare(strict_types=1);

namespace malpka32\InPostBuySdk\Tests\Exception;

use malpka32\InPostBuySdk\Exception\ApiExceptionFactory;
use malpka32\InPostBuySdk\Exception\BadRequestException;
use malpka32\InPostBuySdk\Exception\ForbiddenException;
use malpka32\InPostBuySdk\Exception\NotFoundException;
use malpka32\InPostBuySdk\Exception\UnauthorizedException;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class ApiExceptionFactoryTest extends TestCase
{
    public function testFromResponse400ReturnsBadRequestException(): void
    {
        $response = $this->createResponse(400, '{"errorCode":"BAD_REQUEST","errorMessage":"Invalid"}');
        $e = ApiExceptionFactory::fromResponse($response, 'Context');
        $this->assertInstanceOf(BadRequestException::class, $e);
        $this->assertSame(400, $e->getStatusCode());
        $this->assertSame('Invalid', $e->getMessage());
        $this->assertNotNull($e->getErrorResponse());
        $this->assertSame('BAD_REQUEST', $e->getErrorResponse()->errorCode);
    }

    public function testFromResponse401ReturnsUnauthorizedException(): void
    {
        $response = $this->createResponse(401, '');
        $e = ApiExceptionFactory::fromResponse($response);
        $this->assertInstanceOf(UnauthorizedException::class, $e);
        $this->assertSame(401, $e->getStatusCode());
    }

    public function testFromResponse403ReturnsForbiddenException(): void
    {
        $response = $this->createResponse(403, '{"errorCode":"FORBIDDEN","errorMessage":"Not allowed"}');
        $e = ApiExceptionFactory::fromResponse($response);
        $this->assertInstanceOf(ForbiddenException::class, $e);
        $this->assertSame('Not allowed', $e->getMessage());
    }

    public function testFromResponse404ReturnsNotFoundException(): void
    {
        $body = '{"errorCode":"RESOURCE_NOT_FOUND","errorMessage":"Not found","details":[{"field":"#/id","detail":"Missing"}]}';
        $response = $this->createResponse(404, $body);
        $e = ApiExceptionFactory::fromResponse($response);
        $this->assertInstanceOf(NotFoundException::class, $e);
        $this->assertCount(1, $e->getErrorResponse()->details);
        $this->assertSame('#/id', $e->getErrorResponse()->details[0]->field);
    }

    public function testFromResponseUsesContextMessageWhenNoErrorMessage(): void
    {
        $response = $this->createResponse(500, '{}');
        $e = ApiExceptionFactory::fromResponse($response, 'API error: GET /v1/orders');
        $this->assertSame('API error: GET /v1/orders', $e->getMessage());
    }

    private function createResponse(int $statusCode, string $body): ResponseInterface
    {
        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn($statusCode);
        $response->method('getContent')->with(false)->willReturn($body);
        $response->method('getHeaders')->with(false)->willReturn([]);
        return $response;
    }
}
