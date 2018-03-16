<?php declare(strict_types=1);

namespace Ellipse\Handlers\ADR;

interface DomainInterface
{
    /**
     * Return a payload from the given input.
     *
     * @param array $input
     * @return \Ellipse\Handlers\ADR\PayloadInterface
     */
    public function payload(array $input): PayloadInterface;
}
