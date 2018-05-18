<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Ellipse\ADR\DomainInterface;
use Ellipse\ADR\PayloadInterface;
use Ellipse\Handlers\ContainerDomain;
use Ellipse\Handlers\Exceptions\ContainedDomainTypeException;

describe('ContainerDomain', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->domain = new ContainerDomain($this->container->get(), 'id');

    });

    it('should implement DomainInterface', function () {

        expect($this->domain)->toBeAnInstanceOf(DomainInterface::class);

    });

    describe('->payload()', function () {

        context('The value retrieved from the container implements DomainInterface', function () {

            it('should proxy the ->payload() method of the domain retrieved from the container', function () {

                $delegate = mock(DomainInterface::class);
                $payload = mock(PayloadInterface::class);

                $this->container->get->with('id')->returns($delegate);

                $delegate->payload->with(['key' => 'value'])->returns($payload);

                $test = $this->domain->payload(['key' => 'value']);

                expect($test)->toBe($payload->get());

            });

        });

        context('The value retrieved from the container does not implement DomainInterface', function () {

            it('should throw a ContainedDomainTypeException', function () {

                $this->container->get->with('id')->returns('domain');

                $test = function () {

                    $this->domain->payload(['key' => 'value']);

                };

                $exception = new ContainedDomainTypeException('id', 'domain');

                expect($test)->toThrow($exception);

            });

        });

    });

});
