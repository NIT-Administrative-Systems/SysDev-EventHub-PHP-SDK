<?php

namespace Northwestern\SysDev\SOA\EventHub\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;

class RetryClient
{
    const MAX_RETRIES = 3;

    protected Client $client;

    public static function make(): Client
    {
        $factory = new self();

        return $factory->getClient();
    }

    public function __construct()
    {
        $this->client = $this->createHttpClient();
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    protected function createHttpClient(): Client
    {
        $stack = HandlerStack::create(new CurlHandler());
        $stack->push(\GuzzleHttp\Middleware::retry($this->createRetryHandler()));

        return new Client([
            'handler' => $stack,
        ]);
    }

    protected function createRetryHandler(): callable
    {
        return function (int $retries, Psr7Request $request, ?Psr7Response $response = null, RequestException|ConnectException|null $exception = null) {
            if ($retries >= self::MAX_RETRIES) {
                return false;
            }

            return $this->isConnectError($exception);
        };
    }

    protected function isConnectError(RequestException|ConnectException|null $exception = null): bool
    {
        return $exception instanceof ConnectException;
    }
}
