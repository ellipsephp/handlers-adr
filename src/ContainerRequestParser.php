<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;

use Ellipse\Handlers\Exceptions\ContainedRequestParserTypeException;

class ContainerRequestParser
{
    /**
     * The application container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The container id of the request parser.
     *
     * @var string
     */
    private $id;

    /**
     * Set up a container request parser with the given app container and
     * request parser id.
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
     * Get the request parser from the container then proxy it.
     *
     * @param \Psr\Http\Message\ServerRequestInterface  $request
     * @return array
     * @throws \Ellipse\Handlers\Exceptions\ContainedRequestParserTypeException
     */
    public function __invoke(ServerRequestInterface $request): array
    {
        $parser = $this->container->get($this->id);

        if (is_callable($parser)) {

            return $parser($request);

        }

        throw new ContainedRequestParserTypeException($this->id, $parser);
    }
}
