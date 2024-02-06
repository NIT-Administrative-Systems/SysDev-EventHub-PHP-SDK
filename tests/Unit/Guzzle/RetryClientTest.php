<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit\Guzzle;

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Northwestern\SysDev\SOA\EventHub\Guzzle\RetryClient;
use PHPUnit\Framework\TestCase;

class RetryClientTest extends TestCase
{
    public function testMaxRetries(): void
    {
        /** @var callable $handler */
        $handler = invade($this->client())->createRetryHandler();
        $request = $this->createMock(Psr7Request::class);
        $response = $this->createMock(Psr7Response::class);
        $error = $this->createMock(RequestException::class);

        $firstShouldRetry = $handler(1, $request, $response, $error);
        $lastShouldRetry = $handler(3, $request, $response, $error);

        $this->assertFalse($firstShouldRetry);
        $this->assertFalse($lastShouldRetry);
    }

    public function testConnectionError(): void
    {
        /** @var callable $handler */
        $handler = invade($this->client())->createRetryHandler();
        $request = $this->createMock(Psr7Request::class);
        $response = null;
        $error = $this->createMock(ConnectException::class);

        $shouldRetry = $handler(1, $request, $response, $error);
        $this->assertTrue($shouldRetry);
    }

    protected function client(): RetryClient
    {
        return new RetryClient();
    }
}
