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
     * The payload values.
     *
     * @var array
     */
    private $values;

    /**
     * Set up a payload with the given status and values.
     *
     * @param string    $status
     * @param array     $values
     */
    public function __construct(string $status, array $values = [])
    {
        $this->status = $status;
        $this->values = $values;
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
    public function values(): array
    {
        return $this->values;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'status' => $this->status,
            'values' => $this->values,
        ];
    }
}
