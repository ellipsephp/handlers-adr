<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;
use Ellipse\Handlers\Exceptions\InputTypeException;

class ActionRequestHandler implements RequestHandlerInterface
{
    /**
     * The domain.
     *
     * @var \Ellipse\Handlers\ADR\DomainInterface
     */
    private $domain;

    /**
     * The responder.
     *
     * @var \Ellipse\Handlers\ADR\ResponderInterface
     */
    private $responder;

    /**
     * The input parser.
     *
     * @var callable
     */
    private $parser;

    /**
     * Set up a container request handler with the given container and id.
     *
     * @param \Ellipse\Handlers\ADR\DomainInterface     $domain
     * @param \Ellipse\Handlers\ADR\ResponderInterface  $responder
     * @param callable                                  $parser
     */
    public function __construct(DomainInterface $domain, ResponderInterface $responder, callable $parser)
    {
        $this->domain = $domain;
        $this->responder = $responder;
        $this->parser = $parser;
    }

    /**
     * Return the response produced by the responder with the payload produced
     * by the domain.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Ellipse\Handlers\Exceptions\InputTypeException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = ($this->parser)($request);

        if (is_array($input)) {

            $payload = $this->domain->payload($input);

            return $this->responder->createResponse($request, $payload);

        }

        throw new InputTypeException($input);
    }
}
