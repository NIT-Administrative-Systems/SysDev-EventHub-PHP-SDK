<?php

namespace Northwestern\SysDev\SOA\EventHub;

use Northwestern\SysDev\SOA\EventHub\Model\DeliveredMessage;

class DeadLetterQueue extends EventHubBase
{
    /**
     * Retrieve information about the dead letter queue for a specific queue
     *
     * @param  string  $topic_name  The topic name
     * @param  int  $duration  Period to pull DLQ statistics for
     *
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/getDeadLetterQueueInfo
     */
    public function getInfo(string $topic_name, ?int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', vsprintf('/v1/event-hub/dlq/%s', [$topic_name]), $params);
    }

    /**
     * Get the oldest message in the dead-letter queue
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

        $message = $this->call('get', vsprintf('/v1/event-hub/dlq/%s/message', [$topic_name]), $params);

        // No messages, yay!
        if ($message === true) {
            return null;
        }

        return $message;
    }

    /**
     * Moves a message to the DLQ (dead letter queue) for the specified queue.
     *
     * @param  string  $source_topic_name  The topic name to pull the message from
     * @param  string  $message_id  ID of the message you are moving to the DLQ
     * @param  string  $destination_topic_name  The topic name whose DLQ you want to move a message into
     *
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/moveMessage2
     */
    public function moveToDLQ(string $source_topic_name, string $message_id, string $destination_topic_name): bool
    {
        return $this->call('post', vsprintf('/v1/event-hub/queue/%s/message/%s/dlq/%s', [$source_topic_name, $message_id, $destination_topic_name]));
    }

    /**
     * Moves a message out of the DLQ (dead letter queue) to the specified queue.
     *
     * @param  string  $source_topic_name  The topic name whose DLQ you want to pull the message from
     * @param  string  $message_id  ID of the message you are moving to the DLQ
     * @param  string  $destination_topic_name  The topic name to put the message into
     *
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/moveMessage2
     */
    public function moveFromDLQ(string $source_topic_name, string $message_id, string $destination_topic_name): bool
    {
        return $this->call('post', vsprintf('/v1/event-hub/dlq/%s/message/%s/queue/%s', [$source_topic_name, $message_id, $destination_topic_name]));
    }
}
