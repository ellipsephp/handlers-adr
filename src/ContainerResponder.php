<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\ADR\PayloadInterface;
use Ellipse\Handlers\ResponderInterface;
use Ellipse\Handlers\Exceptions\ContainedResponderTypeException;

class ContainerResponder implements ResponderInterface
{
    /**
     * The application container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The container id of the responder.
     *
     * @var string
     */
    private $id;

    /**
     * Set up a container domain with the given app container and responder id.
     *
     * @param \Psr\Container\ContainerInterface $container
     * @param string                            $id
     */
    public function __construct(ContainerInterface $container, string $id)
    {
        $this->container = $container;
        $this->id = $id;
    }

    /**
     * Get the responder from the container then proxy its response method.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @param \Ellipse\ADR\PayloadInterface             $payload
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Ellipse\Handlers\Exceptions\ContainedResponderTypeException
     */
    public function response(ServerRequestInterface $request, PayloadInterface $payload): ResponseInterface
    {
        $responder = $this->container->get($this->id);

        if ($responder instanceof ResponderInterface) {

            return $responder->response($request, $payload);

        }

        throw new ContainedResponderTypeException($this->id, $responder);
    }
}
