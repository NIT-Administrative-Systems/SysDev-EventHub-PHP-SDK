<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\TestCase;

class ErrorHandling extends TestCase
{
    // Doesn't matter, they all use EventHubBase, which is what we're testing.
    protected $test_class = \Northwestern\SysDev\SOA\EventHub\Topic::class;

    /**
     * @expectedException Northwestern\SysDev\SOA\EventHub\Exception\EventHubDown
     */
    public function test_connection_problem()
    {
        $this->api->setHttpClient($this->mockNetworkConnectivityError());
        $message = $this->api->listAll();
    } // end test_connection_problem

    /**
     * @expectedException Northwestern\SysDev\SOA\EventHub\Exception\EventHubError
     * @expectedExceptionCode 500
     */
    public function test_unauthorized_access()
    {
        $this->api->setHttpClient($this->mockHttpResponse(500, json_encode(['errorCode' => 500, 'errorMessage' => 'Not Authorized'])));
        $queues = $this->api->listAll();
    } // end test_unauthorized_access

} // end ErrorHandling
