<?php declare(strict_types=1);

namespace Ellipse\Handlers\ADR;

use JsonSerializable;

class Payload implements PayloadInterface, JsonSerializable
{
    /**
     * The payload status.
     *
     * @var string
     */
    private $status;

    /**
     * The payload data.
     *
     * @var array
     */
    private $data;

    /**
     * Set up a payload with the given status and data.
     *
     * @param string    $status
     * @param array     $data
     */
    public function __construct(string $status, array $data = [])
    {
        $this->status = $status;
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function status(): string
    {
        return $this->status;
    }

    /**
     * @inheritdoc
     */
    public function data(): array
    {
        return $this->data;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'data' => $this->data,
        ];
    }
}
