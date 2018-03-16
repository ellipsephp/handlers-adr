<?php declare(strict_types=1);

namespace Ellipse\Handlers\ADR;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{
    /**
     * Return a response from the given requrest and payload.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Ellipse\Handlers\ADR\PayloadInterface    $payload
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function createResponse(ServerRequestInterface $request, PayloadInterface $payload): ResponseInterface;
}
