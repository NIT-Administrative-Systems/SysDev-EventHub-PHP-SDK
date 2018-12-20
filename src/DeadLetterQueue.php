<?php

namespace Northwestern\SysDev\SOA\EventHub;

class DeadLetterQueue extends EventHubBase
{
    /**
     * Retrieve information about the dead letter queue for a specific queue
     *
     * @param  string $topic_name The topic name
     * @param  int    $duration   Period to pull DLQ statistics for
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/getDeadLetterQueueInfo
     */
    public function getInfo(string $topic_name, int $duration = null): array
    {
        $params = ($duration === null ? [] : ['duration' => $duration]);

        return $this->call('get', vsprintf('/v1/event-hub/dlq/%s', [$topic_name]), $params);
    } // end getInfo

    /**
     * Moves a message to the DLQ (dead letter queue) for the specified queue.
     *
     * @param  string $destination_topic_name The topic name whose DLQ you want to move a message into
     * @param  string $message_id             ID of the message you are moving to the DLQ
     * @see https://apiserviceregistry.northwestern.edu/#/DLQ/moveMessage2
     */
    public function moveToDLQ(string $source_topic_name,  string $message_id, string $destination_topic_name): bool
    {
        return $this->call('post', vsprintf('/v1/event-hub/queue/%s/message/%s/dlq/%s', [$source_topic_name, $message_id, $destination_topic_name]));
    } // end moveToDLQ

} // end DeadLetterQueue
