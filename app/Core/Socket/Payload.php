<?php


namespace App\Core\Socket;


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
     * @var mixed
     */
    public $message;

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
        $payload = json_decode($strPayload);

        $this->originalPayload = $strPayload;

        $this->command = $payload->command ?? '';

        $this->token = $payload->token ?? '';

        $this->message = $payload->message ?? null;

        $this->time = $payload->time ?? '';


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