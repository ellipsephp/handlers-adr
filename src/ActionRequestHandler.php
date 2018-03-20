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
     * The default input values.
     *
     * @var array
     */
    private $defaults;

    /**
     * Set up an action request handler with the given domain, responder and an
     * array of default input values.
     *
     * @param \Ellipse\Handlers\ADR\DomainInterface     $domain
     * @param \Ellipse\Handlers\ADR\ResponderInterface  $responder
     * @param array                                     $defaults
     */
    public function __construct(DomainInterface $domain, ResponderInterface $responder, array $defaults = [])
    {
        $this->domain = $domain;
        $this->responder = $responder;
        $this->defaults = $defaults;
    }

    /**
     * Create an input array and return the response produced by the responder
     * with the payload produced by the domain.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = array_merge(
            $this->defaults,
            $request->getAttributes(),
            $request->getQueryParams(),
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );

        $payload = $this->domain->payload($input);

        return $this->responder->response($request, $payload);
    }
}
