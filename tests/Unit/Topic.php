<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\TestCase;

class Topic extends TestCase
{
    protected $test_class = \Northwestern\SysDev\SOA\EventHub\Topic::class;

    public function test_list_all(): void
    {
        $response = '[{"eventHubAccount":"sysdev-test-acct","topicName":"etsysdev.test.queue.name","readTimeout":1000,"autoAcknowledge":false,"queueRealName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","alertAddress":"nicholas.evans@northwestern.edu","queueStatistics":[{"label":"QueueSize","maximum":1,"minimum":0,"startTime":"2018-10-30T14:18:00","sum":null,"current":0,"sampleSize":null,"average":0.5},{"label":"EnqueueCount","maximum":null,"minimum":null,"startTime":"2018-10-30T14:18:00","sum":1,"current":null,"sampleSize":null,"average":null},{"label":"EnqueueTime","maximum":2076194,"minimum":5738,"startTime":"2018-10-30T14:18:00","sum":null,"current":null,"sampleSize":null,"average":784116.5967366379},{"label":"ExpiredCount","maximum":null,"minimum":null,"startTime":"2018-10-30T14:18:00","sum":1,"current":null,"sampleSize":null,"average":null},{"label":"DispatchCount","maximum":null,"minimum":null,"startTime":"2018-10-30T14:18:00","sum":9,"current":null,"sampleSize":null,"average":null},{"label":"DequeueCount","maximum":null,"minimum":null,"startTime":"2018-10-30T14:18:00","sum":1,"current":null,"sampleSize":null,"average":null}],"selfPublishingAllowed":true,"destinationType":"QUEUE","name":"etsysdev.test.queue.name","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","hasMessage":false,"dlqName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ"}]';

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
        $response = '{"eventHubAccount":"sysdev-test-acct","topicName":"etsysdev.test.queue.name","readTimeout":1000,"autoAcknowledge":false,"queueRealName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","alertAddress":"nicholas.evans@northwestern.edu","queueStatistics":[{"label":"QueueSize","startTime":"2018-10-30T14:22:00","maximum":1,"minimum":0,"current":0,"sum":null,"average":0.5,"sampleSize":null},{"label":"EnqueueCount","startTime":"2018-10-30T14:22:00","maximum":null,"minimum":null,"current":null,"sum":1,"average":null,"sampleSize":null},{"label":"EnqueueTime","startTime":"2018-10-30T14:22:00","maximum":2076194,"minimum":5738,"current":null,"sum":null,"average":785780.1386910215,"sampleSize":null},{"label":"ExpiredCount","startTime":"2018-10-30T14:22:00","maximum":null,"minimum":null,"current":null,"sum":1,"average":null,"sampleSize":null},{"label":"DispatchCount","startTime":"2018-10-30T14:22:00","maximum":null,"minimum":null,"current":null,"sum":9,"average":null,"sampleSize":null},{"label":"DequeueCount","startTime":"2018-10-30T14:22:00","maximum":null,"minimum":null,"current":null,"sum":1,"average":null,"sampleSize":null}],"selfPublishingAllowed":true,"destinationType":"QUEUE","name":"etsysdev.test.queue.name","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","hasMessage":false,"dlqName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ"}';

        // With period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queue = $this->api->getInfo('etsysdev.test.queue.name', 120);
        $this->assertEquals('etsysdev.test.queue.name', $queue['topicName']);

        // Without period arg
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));
        $queue = $this->api->getInfo('etsysdev.test.queue.name');
        $this->assertEquals('etsysdev.test.queue.name', $queue['topicName']);
    } // end test_get_info

    public function test_configure(): void
    {
        $response = '{"eventHubAccount":"sysdev-test-acct","topicName":"etsysdev.test.queue.name","readTimeout":1000,"autoAcknowledge":false,"queueRealName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","alertAddress":"nicholas.evans@northwestern.edu","queueStatistics":[],"selfPublishingAllowed":true,"destinationType":"QUEUE","name":"etsysdev.test.queue.name","realName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name","hasMessage":false,"dlqName":"Consumer.sysdev-test-acct.VirtualTopic.etsysdev.test.queue.name.DLQ"}';
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));

        $config = $this->api->configure('etsysdev.test.queue.name', []);
        $this->assertEquals('etsysdev.test.queue.name', $config['topicName']);
    } // end test_configure

    public function test_write_json_message(): void
    {
        $response_id = 'ID:12345:baz';
        $this->api->setHttpClient($this->mockHttpResponse(204, null, ['X-message-id' => $response_id]));

        $message_id = $this->api->writeJsonMessage('etsysdev.test.queue.name', ['testing' => 'messaging', 'is' => 'fun']);
        $this->assertEquals($response_id, $message_id);
    } // end test_write_json_message

    public function test_write_message(): void
    {
        $response_id = 'ID:12345:baz';
        $this->api->setHttpClient($this->mockHttpResponse(204, null, ['X-message-id' => $response_id]));

        $message_id = $this->api->writeMessage('etsysdev.test.queue.name', '{"foo": "bar"}', 'application/json');
        $this->assertEquals($response_id, $message_id);
    } // end test_write_message

} // end Topic
