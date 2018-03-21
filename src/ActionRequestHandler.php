<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;
use Ellipse\Handlers\Exceptions\ContainedDomainTypeException;
use Ellipse\Handlers\Exceptions\ContainedResponderTypeException;

class ActionRequestHandler implements RequestHandlerInterface
{
    /**
     * The container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The container entry id for the domain.
     *
     * @var string
     */
    private $domain;

    /**
     * The container entry id for the responder.
     *
     * @var string
     */
    private $responder;

    /**
     * The default input array.
     *
     * @var array
     */
    private $defaults;

    /**
     * Set up an action request handler with the given container, container ids
     * for domain and responder, and optional default input array.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $domain
     * @param string                            $responder
     * @param array                             $defaults
     */
    public function __construct(ContainerInterface $container, string $domain, string $responder, array $defaults = [])
    {
        $this->container = $container;
        $this->domain = $domain;
        $this->responder = $responder;
        $this->defaults = $defaults;
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

        $payload = $this->domain()->payload($input);

        return $this->responder()->response($request, $payload);
    }

    /**
     * Return an input array from the given request.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return array
     */
    private function input(ServerRequestInterface $request): array
    {
        return array_merge(
            $this->defaults,
            $request->getAttributes(),
            $request->getQueryParams(),
            $request->getParsedBody(),
            $request->getUploadedFiles()
        );
    }

    /**
     * Return the container entry associated with the domain container id.
     *
     * @return \Ellipse\Handlers\ADR\DomainInterface
     * @throws \Ellipse\Handlers\Exceptions\ContainedDomainTypeException
     */
    private function domain(): DomainInterface
    {
        $domain = $this->container->get($this->domain);

        if ($domain instanceof DomainInterface) {

            return $domain;

        }

        throw new ContainedDomainTypeException($this->domain, $domain);
    }

    /**
     * Return the container entry associated with the responder container id.
     *
     * @return \Ellipse\Handlers\ADR\ResponderInterface
     * @throws \Ellipse\Handlers\Exceptions\ContainedResponderTypeException
     */
    private function responder(): ResponderInterface
    {
        $responder = $this->container->get($this->responder);

        if ($responder instanceof ResponderInterface) {

            return $responder;

        }

        throw new ContainedResponderTypeException($this->responder, $responder);
    }
}
