<?php

namespace Northwestern\SysDev\SOA\EventHub\Guzzle;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request as Psr7Request;
use GuzzleHttp\Psr7\Response as Psr7Response;

class RetryClient
{
    const MAX_RETRIES = 3;
    protected $client;

    public static function make()
    {
        $factory = new self();

        return $factory->getClient();
    } // end make

    public function __construct()
    {
        $this->client = $this->createHttpClient();
    } // end __construct

    public function getClient()
    {
        return $this->client;
    } // end getClient

    protected function createHttpClient()
    {
        $stack = HandlerStack::create(new CurlHandler());
        $stack->push(\GuzzleHttp\Middleware::retry($this->createRetryHandler()));
        $client = new Client([
            'handler' => $stack,
        ]);

        return $client;
    } // end createHttpClient

    protected function createRetryHandler()
    {
        return function ($retries, Psr7Request $request, Psr7Response $response = null, RequestException|ConnectException $exception = null) {
            if ($retries >= self::MAX_RETRIES) {
                return false;
            }

            return $this->isConnectError($exception);
        };
    } // end createRetryHandler

    protected function isConnectError(RequestException|ConnectException $exception = null)
    {
        return $exception instanceof ConnectException;
    } // end isConnectError

} // end RetryClient
