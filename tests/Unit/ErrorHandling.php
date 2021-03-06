<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\TestCase;

class ErrorHandling extends TestCase
{
    // Doesn't matter, they all use EventHubBase, which is what we're testing.
    protected $test_class = \Northwestern\SysDev\SOA\EventHub\Topic::class;

    public function test_connection_problem()
    {
        $this->expectException(\Northwestern\SysDev\SOA\EventHub\Exception\EventHubDown::class);

        $this->api->setHttpClient($this->mockNetworkConnectivityError());
        
        $this->api->listAll();
    } // end test_connection_problem

    public function test_unauthorized_access()
    {
        $this->expectException(\Northwestern\SysDev\SOA\EventHub\Exception\EventHubError::class);
        $this->expectExceptionCode(500);

        $this->api->setHttpClient($this->mockHttpResponse(500, json_encode(['errorCode' => 500, 'errorMessage' => 'Not Authorized'])));
        
        $this->api->listAll();
    } // end test_unauthorized_access

} // end ErrorHandling
