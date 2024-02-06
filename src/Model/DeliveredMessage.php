<?php

namespace Northwestern\SysDev\SOA\EventHub\Model;

class DeliveredMessage
{
    protected string $id;

    protected ?array $deserialized_message;

    protected string $raw_message;

    public function __construct(string $id, string $message)
    {
        $this->id = $id;
        $this->raw_message = $message;
        $this->deserialized_message = json_decode($message, true);
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getMessage(): ?array
    {
        return $this->deserialized_message;
    }

    public function getRawMessage(): string
    {
        return $this->raw_message;
    }
}
