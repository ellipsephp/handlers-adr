<?php declare(strict_types=1);

namespace Ellipse\Handlers\ADR;

interface PayloadInterface
{
    /**
     * Return the payload status.
     *
     * @return string
     */
    public function status(): string;

    /**
     * Return the payload data.
     *
     * @return array
     */
    public function data(): array;
}
