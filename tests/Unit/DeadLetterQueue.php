<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\TestCase;

class DeadLetterQueue extends TestCase
{
    protected $test_class = \Northwestern\SysDev\SOA\EventHub\DeadLetterQueue::class;

    public function test_get_info(): void
    {
        $response = '{"name":"etsysdev.test.queue.name.DLQ","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ","eventHubAccount":"sysdev-test-acct","destinationType":"DLQ","queueStatistics":[{"label":"QueueSize","maximum":null,"minimum":null,"startTime":null,"sum":null,"current":null,"sampleSize":null,"average":null},{"label":"EnqueueCount","maximum":null,"minimum":null,"startTime":null,"sum":null,"current":null,"sampleSize":null,"average":null},{"label":"EnqueueTime","maximum":null,"minimum":null,"startTime":null,"sum":null,"current":null,"sampleSize":null,"average":null},{"label":"ExpiredCount","maximum":null,"minimum":null,"startTime":null,"sum":null,"current":null,"sampleSize":null,"average":null},{"label":"DispatchCount","maximum":null,"minimum":null,"startTime":null,"sum":null,"current":null,"sampleSize":null,"average":null},{"label":"DequeueCount","maximum":null,"minimum":null,"startTime":null,"sum":null,"current":null,"sampleSize":null,"average":null}]}';

        // with period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $dlq = $this->api->getInfo('etsysdev.test.queue.name', 120);
        $this->assertEquals('etsysdev.test.queue.name.DLQ', $dlq['name']);

        // without period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $dlq = $this->api->getInfo('etsysdev.test.queue.name');
        $this->assertEquals('etsysdev.test.queue.name.DLQ', $dlq['name']);
    } // end test_get_info

    public function test_move_to_dlq(): void
    {
        $this->api->setHttpClient($this->mockHttpResponse(204, null));

        $status = $this->api->moveToDLQ('etsysdev.test.queue.name', 'ID:1234:baz', 'etsysdev.test.queue.name');
        $this->assertTrue($status);
    } // end test_move_to_dlq

    public function test_read_oldest(): void
    {
        $response_id = 'ID:12345:baz';
        $response_body = ['cool' => 'message'];

        // Without autoAck arg
        $this->api->setHttpClient($this->mockHttpResponse(200, json_encode($response_body), ['X-message-id' => $response_id]));
        $message = $this->api->readOldest('etsysdev.test.queue.name');
        $this->assertEquals($response_id, $message->getId());
        $this->assertEquals($response_body, $message->getMessage());

        // With autoAck arg
        $this->api->setHttpClient($this->mockHttpResponse(200, json_encode($response_body), ['X-message-id' => $response_id]));
        $message = $this->api->readOldest('etsysdev.test.queue.name', true);
        $this->assertEquals($response_id, $message->getId());
        $this->assertEquals($response_body, $message->getMessage());

        // Nothing in queue
        $this->api->setHttpClient($this->mockHttpResponse(204, null));
        $message = $this->api->readOldest('etsysdev.test.queue.name');
        $this->assertNull($message);
    } // end test_read_oldest

    public function test_move_from_dlq(): void
    {
        $this->api->setHttpClient($this->mockHttpResponse(204, null));

        $status = $this->api->moveFromDLQ('etsysdev.test.queue.name', 'ID:1234:baz', 'etsysdev.test.queue.name');
        $this->assertTrue($status);
    } // end test_move_from_dlq

} // end DeadLetterQueue
