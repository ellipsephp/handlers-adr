<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;
use Ellipse\Handlers\ADR\RequestParserInterface;
use Ellipse\Handlers\ADR\DefaultRequestParser;

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
     * The request parser.
     *
     * @var \Ellipse\Handlers\ADR\RequestParserInterface
     */
    private $parser;

    /**
     * Set up an ADR request handler with the given domain, responder and an
     * optional request parser.
     *
     * @param \Ellipse\Handlers\ADR\DomainInterface         $domain
     * @param \Ellipse\Handlers\ADR\ResponderInterface      $responder
     * @param \Ellipse\Handlers\ADR\RequestParserInterface  $parser
     */
    public function __construct(DomainInterface $domain, ResponderInterface $responder, RequestParserInterface $parser = null)
    {
        $this->domain = $domain;
        $this->responder = $responder;
        $this->parser = $parser ?? new DefaultRequestParser;
    }

    /**
     * Return the response produced by the responder with the payload produced
     * by the domain.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = $this->parser->input($request);

        $payload = $this->domain->payload($input);

        return $this->responder->response($request, $payload);
    }
}
