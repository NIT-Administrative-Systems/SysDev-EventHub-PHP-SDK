<?php

namespace Northwestern\SysDev\SOA\EventHub\Tests\Unit;

use Northwestern\SysDev\SOA\EventHub\Tests\TestCase;
use Northwestern\SysDev\SOA\EventHub\Model\DeliveredMessage as DeliveredMessageModel; // same name as the class, needs an alias to work

class DeliveredMessage extends TestCase
{
    public function test_opens_json_message(): void
    {
        $msg = new DeliveredMessageModel('ID:12345', '{"test": true}');

        $this->assertArrayHasKey('test', $msg->getMessage());
    } // end test_opens_json_message

    public function test_raw_xml_available(): void
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?><note><to>Tove</to><from>Jani</from><heading>Reminder</heading><body>Do the thing this weekend!</body></note>';
        $msg = new DeliveredMessageModel('ID:12345', $xml);

        $this->assertNull($msg->getMessage());
        $this->assertEquals($xml, $msg->getRawMessage());
    } // end test_raw_xml_available

    public function test_has_message_id(): void
    {
        $id = 'ID:12345';
        $msg = new DeliveredMessageModel($id, '');

        $this->assertEquals($id, $msg->getId());
    } // end test_has_message_id

} // end DeliveredMessage
