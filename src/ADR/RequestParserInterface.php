<?php declare(strict_types=1);

namespace Ellipse\Handlers\ADR;

use Psr\Http\Message\ServerRequestInterface;

interface RequestParserInterface
{
    /**
     * Return an input array from the given request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    public function input(ServerRequestInterface $request): array;
}
