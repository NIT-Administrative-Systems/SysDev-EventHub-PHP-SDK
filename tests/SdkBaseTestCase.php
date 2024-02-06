<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\RequestException;
use Northwestern\SysDev\SOA\EventHub\EventHubBase;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class SdkBaseTestCase extends BaseTestCase
{
    protected ?EventHubBase $api = null;

    /**
     * @var class-string|null
     */
    protected ?string $test_class = null;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->test_class !== null) {
            $this->api = new $this->test_class('https://northwestern.edu', bin2hex(random_bytes(18)), new Client);
        }
    }

    protected function mockHttpResponse($status_code, $body, $headers = []): Client
    {
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);

        $mock = new MockHandler([
            new Response($status_code, $headers, $body),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }

    protected function mockNetworkConnectivityError(): Client
    {
        $mock = new MockHandler([
            new RequestException('Connection timed out', new Request('GET', 'dummy')),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    }
}
