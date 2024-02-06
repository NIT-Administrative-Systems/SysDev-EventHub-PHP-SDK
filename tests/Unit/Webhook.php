<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\SdkBaseTestCase;

final class Webhook extends SdkBaseTestCase
{
    protected ?string $test_class = \Northwestern\SysDev\SOA\EventHub\Webhook::class;

    public function test_list_all(): void
    {
        $response = '{"eventHubAccount":"sysdev-test-acct","webhooks":[{"topicName":"etsysdev.test.queue.name","callbackURL":"\/v1\/event-hub\/webhook\/etsysdev.test.queue.name"}]}';
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));

        $hooks = $this->api->listAll();
        $this->assertArrayHasKey('webhooks', $hooks);
    }

    public function test_get_info(): void
    {
        $response = '{"topicName":"etsysdev.test.queue.name","endpoint":"http:\/\/localhost:9080\/GenericMQ\/test\/apikey","securityTypes":["NONE"],"contentType":"application\/json","active":false,"webhookSecurity":[],"webhookStatistics":[],"event_hub_account":"sysdev-test-acct"}';
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));

        $hook = $this->api->getInfo('etsysdev.test.queue.name');
        $this->assertEquals('etsysdev.test.queue.name', $hook['topicName']);
    }

    public function test_delete(): void
    {
        $this->api->setHttpClient($this->mockHttpResponse(204, ''));

        $status = $this->api->delete('etsysdev.test.queue.name');
        $this->assertTrue($status);
    }

    public function test_create(): void
    {
        $this->api->setHttpClient($this->mockHttpResponse(204, ''));

        $status = $this->api->create('etsysdev.test.queue.name', ['topicName' => 'etsysdev.test.queue.name', 'endpoint' => 'http://localhost:9080/GenericMQ/test/apikey', 'contentType' => 'application/json', 'active' => false, 'securityTypes' => ['NONE'], 'webhookSecurity' => [['securityType' => 'NONE']]]);
        $this->assertTrue($status);
    }

    public function test_update_config(): void
    {
        $response = '{"topicName":"etsysdev.test.queue.name","endpoint":"http:\/\/localhost:9080\/GenericMQ\/test\/apikey","securityTypes":["NONE"],"contentType":"application\/json","active":true,"webhookSecurity":[],"webhookStatistics":null,"event_hub_account":"sysdev-test-acct"}';
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));

        $hook = $this->api->updateConfig('etsysdev.test.queue.name', []);
        $this->assertEquals('etsysdev.test.queue.name', $hook['topicName']);
    }

    public function test_pause(): void
    {
        $response = '{"topicName":"etsysdev.test.queue.name","endpoint":"http:\/\/localhost:9080\/GenericMQ\/test\/apikey","securityTypes":["NONE"],"contentType":"application\/json","active":false,"webhookSecurity":[],"webhookStatistics":null,"event_hub_account":"sysdev-test-acct"}';
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));

        $hook = $this->api->pause('etsysdev.test.queue.name', []);
        $this->assertEquals('etsysdev.test.queue.name', $hook['topicName']);
    }

    public function test_unpause(): void
    {
        $response = '{"topicName":"etsysdev.test.queue.name","endpoint":"http:\/\/localhost:9080\/GenericMQ\/test\/apikey","securityTypes":["NONE"],"contentType":"application\/json","active":true,"webhookSecurity":[],"webhookStatistics":null,"event_hub_account":"sysdev-test-acct"}';
        $this->api->setHttpClient($this->mockHttpResponse(200, $response));

        $hook = $this->api->unpause('etsysdev.test.queue.name', []);
        $this->assertEquals('etsysdev.test.queue.name', $hook['topicName']);
    }
}
