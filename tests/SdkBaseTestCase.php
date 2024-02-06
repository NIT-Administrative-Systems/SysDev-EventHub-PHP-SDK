<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Exception\RequestException;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class SdkBaseTestCase extends BaseTestCase
{
    protected $api;
    protected $test_class;

    protected function setUp(): void
    {
        parent::setUp();

        if ($this->test_class !== null) {
            $this->api = new $this->test_class('https://northwestern.edu', bin2hex(random_bytes(18)), new Client);
        }
    } // end setUp

    protected function mockHttpResponse($status_code, $body, $headers = [])
    {
        $headers = array_merge(['Content-Type' => 'application/json'], $headers);

        $mock = new MockHandler([
            new Response($status_code, $headers, $body),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    } // end mockedResponse

    protected function mockNetworkConnectivityError()
    {
        $mock = new MockHandler([
            new RequestException('Connection timed out', new Request('GET', 'dummy')),
        ]);

        return new Client(['handler' => HandlerStack::create($mock)]);
    } // end mockedConnError

} // end TestCase
