<?php


namespace App\Core\Socket;


class Payload
{
    /**
     * Sent request command
     * @var mixed
     */
    public $command;
    /**
     * Sent request message
     * @var mixed
     */
    public $payload;
    /**
     * Request time
     * @var mixed
     */
    public $time;
    protected string $originalPayload;

    public static function init(string $payload)
    {
        return new self($payload);
    }

    public function loadOriginal()
    {
        $this->__construct($this->originalPayload);

        return $this;
    }

    public function __construct(string $strPayload)
    {
        $payload = (array)json_decode($strPayload);

        $this->originalPayload = $strPayload;

        $this->command = $payload->command ?? null;

        $this->message = $payload->message ?? null;

        $this->time = $payload->time ?? null;

        foreach ($payload as $item => $value) {
            $this->$item = $value;
        }
    }

    /**
     * @return string
     */
    public function getOriginalPayload(): string
    {
        return $this->originalPayload;
    }
}