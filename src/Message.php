<?php

namespace Northwestern\SysDev\SOA\EventHub;

use Northwestern\SysDev\SOA\EventHub\Model\DeliveredMessage;

class Message extends EventHubBase
{
    /**
     * Reads a message from a queue you are authorized to access.
     *
     * @param  string  $topic_name  The topic name
     * @param  bool  $acknowledge  Auto-ack (auto-delete), true or false
     * @return null|DeliveredMessage Returns null if the queue is empty
     *
     * @see https://apiserviceregistry.northwestern.edu/#/message/getMessages
     */
    public function readOldest(string $topic_name, ?bool $acknowledge = null): ?DeliveredMessage
    {
        $params = ($acknowledge === null ? [] : ['acknowledge' => $this->stringifyBool($acknowledge)]);

        $message = $this->call('get', vsprintf('/v1/event-hub/queue/%s/message', [$topic_name]), $params);

        // No messages, yay!
        if ($message === true) {
            return null;
        }

        return $message;
    }

    /**
     * Acknowledges (Deletes) the oldest message from a queue
     *
     * @param  string  $topic_name  The topic name
     *
     * @see https://apiserviceregistry.northwestern.edu/#/message/acknowledgeOldestMessage
     */
    public function acknowledgeOldest(string $topic_name): bool
    {
        return $this->call('delete', vsprintf('/v1/event-hub/queue/%s/message', [$topic_name]));
    }

    /**
     * Retrieve a specific message from a queue
     *
     * @param  string  $topic_name  The topic name
     * @param  string  $message_id  The message ID
     *
     * @see https://apiserviceregistry.northwestern.edu/#/message/getSpecificMessage
     */
    public function read(string $topic_name, string $message_id): DeliveredMessage
    {
        // @TODO doesn't work, returns a NYI string instead of a msg
        return $this->call('get', vsprintf('/v1/event-hub/queue/%s/message/%s', [$topic_name, $message_id]));
    }

    /**
     * Acknowledge (Delete) a message or group of message from a queue
     *
     * @param  string  $topic_name  The topic name
     * @param  string  $message_id  The message ID
     * @param  bool  $fast_forward  If set to true the message represented by the message identifier and all older messages in the queue will be acknowledged.
     *
     * @see https://apiserviceregistry.northwestern.edu/#/message/acknowledgeMessage
     */
    public function acknowledge(string $topic_name, string $message_id, ?bool $fast_forward = null): bool
    {
        $params = ($fast_forward === null ? [] : ['fast_forward' => $this->stringifyBool($fast_forward)]);

        return $this->call('delete', vsprintf('/v1/event-hub/queue/%s/message/%s', [$topic_name, $message_id]), $params);
    }

    /**
     * Allows you to move a message from a queue you own to another queue/topic you own. This could be done via a get and subsequent write call. It also allows for the moving of a message to the queues corresponding DLQ (dead letter queue), or to redeliver the message to the same queue but with a delivery delay.
     *
     * @param  string  $source_topic_name  Source topic name
     * @param  string  $message_id  The message ID to move
     * @param  string  $destination_type  Type of destination you want to move this message to {QUEUE | TOPIC}
     * @param  string  $destination_topic_name  Name of the destination you are moving this message to
     * @param  int  $delay  Delay in milliseconds to wait before delivering this message
     *
     * @see https://apiserviceregistry.northwestern.edu/#/message/moveMessage1
     */
    public function move(string $source_topic_name, string $message_id, string $destination_type, string $destination_topic_name, ?int $delay = null): bool
    {
        $params = ($delay === null ? [] : ['delay' => $delay]);

        // This one is kinda long & was pretty unreadable on one line, so...
        $url = vsprintf('/v1/event-hub/queue/%s/message/%s/%s/%s', [
            $source_topic_name,
            $message_id,
            $destination_type,
            $destination_topic_name,
        ]);

        return $this->call('post', $url, $params);
    }
}
