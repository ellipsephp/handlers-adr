<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\ADR\PayloadInterface;

interface ResponderInterface
{
    /**
     * Return a response from the given requrest and payload.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Ellipse\ADR\PayloadInterface             $payload
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function response(ServerRequestInterface $request, PayloadInterface $payload): ResponseInterface;
}
