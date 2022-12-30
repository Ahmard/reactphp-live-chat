<?php


namespace Server\Websocket;


use Server\Exceptions\Socket\InvalidPayloadException;

class Payload
{
    /**
     * Sent request command
     * @var string
     */
    public string $command;

    /**
     * Request time
     * @var float
     */
    public float $time;

    /**
     * Auth token sent
     * @var string
     */
    public string $token;

    /**
     * @var string|null
     */
    public $message;

    protected string $originalPayload;

    public static function init(string $payload): Payload
    {
        return new Payload($payload);
    }

    public function loadOriginal(): Payload
    {
        $this->__construct($this->originalPayload);

        return $this;
    }

    public function __construct(string $strPayload)
    {
        $payload = json_decode($strPayload);

        $this->originalPayload = $strPayload;

        if (!$payload->command) {
            InvalidPayloadException::create('No payload command specified.');
        }

        $this->command = $payload->command ?? '';

        $this->token = $payload->token ?? '';

        $this->message = $payload->message ?? null;

        $this->time = (float)($payload->time ?? 0);


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