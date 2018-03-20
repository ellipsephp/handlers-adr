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

class ContainerActionRequestHandler implements RequestHandlerInterface
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
     * The default input values.
     *
     * @var array
     */
    private $defaults;

    /**
     * Set up a container action request handler with the given container,
     * container ids for domain and responder, and default input values.
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
     * Create an action request handler with the container entries and proxy it.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Ellipse\Handlers\Exceptions\ContainedDomainTypeException
     * @throws \Ellipse\Handlers\Exceptions\ContainedResponderTypeException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $domain = $this->container->get($this->domain);
        $responder = $this->container->get($this->responder);

        if (! $domain instanceof DomainInterface) {

            throw new ContainedDomainTypeException($this->domain, $domain);

        }

        if (! $responder instanceof ResponderInterface) {

            throw new ContainedResponderTypeException($this->responder, $responder);

        }

        $handler = new ActionRequestHandler($domain, $responder, $this->defaults);

        return $handler->handle($request);
    }
}
