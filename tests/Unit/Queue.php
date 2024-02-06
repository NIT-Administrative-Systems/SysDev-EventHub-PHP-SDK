<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\TestCase;

final class Queue extends TestCase
{
    protected $test_class = \Northwestern\SysDev\SOA\EventHub\Queue::class;

    public function test_list_all(): void
    {
        $response = '[{"eventHubAccount":"sysdev-test-acct","topicName":"etsysdev.test.queue.name","readTimeout":1000,"autoAcknowledge":false,"queueRealName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","alertAddress":"nicholas.evans@northwestern.edu","queueStatistics":[{"label":"QueueSize","maximum":0,"minimum":0,"startTime":"2018-10-31T06:39:00","sum":null,"average":0,"current":0,"sampleSize":null},{"label":"EnqueueCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null},{"label":"EnqueueTime","maximum":2076194,"minimum":5738,"startTime":"2018-10-31T06:39:00","sum":null,"average":791356.4578313242,"current":null,"sampleSize":null},{"label":"ExpiredCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null},{"label":"DispatchCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null},{"label":"DequeueCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null}],"selfPublishingAllowed":true,"destinationType":"QUEUE","name":"etsysdev.test.queue.name","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","hasMessage":false,"dlqName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ"}]';

        // With period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queues = $this->api->listAll(120);
        $this->assertEquals('etsysdev.test.queue.name', $queues[0]['topicName']);

        // Without period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queues = $this->api->listAll();
        $this->assertEquals('etsysdev.test.queue.name', $queues[0]['topicName']);
    } // end test_list_all

    public function test_get_info(): void
    {
        $response = '{"eventHubAccount":"sysdev-test-acct","topicName":"etsysdev.test.queue.name","readTimeout":1000,"autoAcknowledge":false,"queueRealName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","alertAddress":"nicholas.evans@northwestern.edu","queueStatistics":[{"label":"QueueSize","maximum":0,"minimum":0,"startTime":"2018-10-31T06:39:00","sum":null,"average":0,"current":0,"sampleSize":null},{"label":"EnqueueCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null},{"label":"EnqueueTime","maximum":2076194,"minimum":5738,"startTime":"2018-10-31T06:39:00","sum":null,"average":791356.4578313242,"current":null,"sampleSize":null},{"label":"ExpiredCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null},{"label":"DispatchCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null},{"label":"DequeueCount","maximum":null,"minimum":null,"startTime":"2018-10-31T06:39:00","sum":0,"average":null,"current":null,"sampleSize":null}],"selfPublishingAllowed":true,"destinationType":"QUEUE","name":"etsysdev.test.queue.name","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","hasMessage":false,"dlqName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ"}';

        // With period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queue = $this->api->getInfo('etsysdev.test.queue.name', 120);
        $this->assertEquals('etsysdev.test.queue.name', $queue['topicName']);

        // Without period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queue = $this->api->getInfo('etsysdev.test.queue.name');
        $this->assertEquals('etsysdev.test.queue.name', $queue['topicName']);
    } // end test_get_info

    public function test_clear_all_messages(): void
    {
        $this->api->setHttpClient($this->mockHttpResponse(204, ''));
        $status = $this->api->clearAllMessages('etsysdev.test.queue.name');

        $this->assertTrue($status);
    } // end test_clear_all_messages

    public function test_configure(): void
    {
        $response = '{"eventHubAccount":"sysdev-test-acct","topicName":"etsysdev.test.queue.name","readTimeout":1000,"autoAcknowledge":false,"queueRealName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","alertAddress":"nicholas.evans@northwestern.edu","queueStatistics":[],"selfPublishingAllowed":true,"destinationType":"QUEUE","name":"etsysdev.test.queue.name","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","hasMessage":false,"dlqName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ"}';

        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queue = $this->api->configure('etsysdev.test.queue.name', []);
        $this->assertEquals('etsysdev.test.queue.name', $queue['topicName']);
    } // end test_configure

    public function test_send_test_json_message(): void
    {
        $response_id = 'ID:12345:baz';
        $this->api->setHttpClient($this->mockHttpResponse(204, null, ['X-message-id' => $response_id]));

        $message_id = $this->api->sendTestJsonMessage('etsysdev.test.queue.name', ['testing' => 'messaging', 'is' => 'fun']);
        $this->assertEquals($response_id, $message_id);
    } // end test_send_test_json_message

    public function test_send_test_message(): void
    {
        $response_id = 'ID:12345:baz';
        $this->api->setHttpClient($this->mockHttpResponse(204, null, ['X-message-id' => $response_id]));

        $message_id = $this->api->sendTestMessage('etsysdev.test.queue.name', '{"foo": true}', 'application/json');
        $this->assertEquals($response_id, $message_id);
    } // end test_send_test_message

} // end Queue
