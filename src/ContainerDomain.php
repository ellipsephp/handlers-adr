<?php declare(strict_types=1);

namespace Ellipse\Handlers;

use Psr\Container\ContainerInterface;

use Ellipse\ADR\DomainInterface;
use Ellipse\ADR\PayloadInterface;
use Ellipse\Handlers\Exceptions\ContainedDomainTypeException;

class ContainerDomain implements DomainInterface
{
    /**
     * The application container.
     *
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * The container id of the domain.
     *
     * @var string
     */
    private $id;

    /**
     * Set up a container domain with the given app container and domain id.
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
     * Get the domain from the container then proxy its payload method.
     *
     * @param array $input
     * @return \Ellipse\ADR\PayloadInterface
     * @throws \Ellipse\Handlers\Exceptions\ContainedDomainTypeException
     */
    public function payload(array $input): PayloadInterface
    {
        $domain = $this->container->get($this->id);

        if ($domain instanceof DomainInterface) {

            return $domain->payload($input);

        }

        throw new ContainedDomainTypeException($this->id, $domain);
    }
}
