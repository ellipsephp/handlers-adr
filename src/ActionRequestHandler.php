<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\ADR\DomainInterface;
use Ellipse\Handlers\ResponderInterface;
use Ellipse\Handlers\Exceptions\InputTypeException;

class ActionRequestHandler implements RequestHandlerInterface
{
    /**
     * The domain.
     *
     * @var \Ellipse\ADR\DomainInterface
     */
    private $domain;

    /**
     * The responder.
     *
     * @var \Ellipse\Handlers\ResponderInterface
     */
    private $responder;

    /**
     * The request parser.
     *
     * @var callable|null
     */
    private $parser;

    /**
     * Set up an action request handler with the given domain, responder and an
     * optional request parser.
     *
     * @param \Ellipse\ADR\DomainInterface          $domain
     * @param \Ellipse\Handlers\ResponderInterface  $responder
     * @param callable|null                         $parser
     */
    public function __construct(DomainInterface $domain, ResponderInterface $responder, callable $parser = null)
    {
        $this->domain = $domain;
        $this->responder = $responder;
        $this->parser = $parser;
    }

    /**
     * Return a response by following the action domain responder pattern.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $input = $this->input($request);

        $payload = $this->domain->payload($input);

        return $this->responder->response($request, $payload);
    }

    /**
     * Return an input array from the given request using the request parser.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     * @throws \Ellipse\Handlers\Exceptions\InputTypeException
     */
    private function input(ServerRequestInterface $request): array
    {
        if (! is_null($this->parser)) {

            $input = ($this->parser)($request);

            if (is_array($input)) {

                return $input;

            }

            throw new InputTypeException($input);

        }

        return array_merge(
            $request->getAttributes(),
            $request->getQueryParams(),
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );
    }
}
