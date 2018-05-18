<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;

use Ellipse\Handlers\ContainerRequestParser;
use Ellipse\Handlers\Exceptions\ContainedRequestParserTypeException;

describe('ContainerRequestParser', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->parser = new ContainerRequestParser($this->container->get(), 'id');

    });

    it('should be callable', function () {

        $test = is_callable($this->parser);

        expect($test)->toBeTruthy();

    });

    describe('->__invoke()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class);

        });

        context('The value retrieved from the container is a callable', function () {

            it('should proxy the request parser retrieved from the container', function () {

                $parser = stub();

                $this->container->get->with('id')->returns($parser);

                $parser->with($this->request)->returns(['key' => 'value']);

                $test = ($this->parser)($this->request->get());

                expect($test)->toEqual(['key' => 'value']);

            });

        });

        context('The value retrieved from the container is not a callable', function () {

            it('should throw a ContainedRequestParserTypeException', function () {

                $this->container->get->with('id')->returns('parser');

                $test = function () {

                    ($this->parser)($this->request->get());

                };

                $exception = new ContainedRequestParserTypeException('id', 'parser');

                expect($test)->toThrow($exception);

            });

        });

    });

});
