<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\SdkBaseTestCase;

final class Message extends SdkBaseTestCase
{
    protected $test_class = \Northwestern\SysDev\SOA\EventHub\Message::class;

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

    public function test_acknowledge_oldest(): void
    {
        $this->api->setHttpClient($this->mockHttpResponse(204, null));
        $status = $this->api->acknowledgeOldest('etsysdev.test.queue.name');
        $this->assertTrue($status);
    } // end test_acknowledge_oldest

    public function test_read(): void
    {
        // Does not currently work in EventHub
        $this->markTestIncomplete();
    } // end test_read

    public function test_acknowledge(): void
    {
        // With fastForward arg
        $this->api->setHttpClient($this->mockHttpResponse(204, null));
        $status = $this->api->acknowledge('etsysdev.test.queue.name', 'ID:foobar:baz');
        $this->assertTrue($status);

        // Without fastForward arg
        $this->api->setHttpClient($this->mockHttpResponse(204, null));
        $status = $this->api->acknowledge('etsysdev.test.queue.name', 'ID:foobar:baz', true);
        $this->assertTrue($status);
    } // end test_acknowledge

    public function test_move(): void
    {
        // With delay arg
        $this->api->setHttpClient($this->mockHttpResponse(204, null));
        $status = $this->api->move('etsysdev.test.queue.name', 'ID:foobar:baz', 'TOPIC', 'etsysdev.test.queue.name', 2500);
        $this->assertTrue($status);

        // Without delay arg
        $this->api->setHttpClient($this->mockHttpResponse(204, null));
        $status = $this->api->move('etsysdev.test.queue.name', 'ID:foobar:baz', 'TOPIC', 'etsysdev.test.queue.name');
        $this->assertTrue($status);
    } // end test_move

} // end Message
