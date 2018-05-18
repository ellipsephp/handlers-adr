<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\ADR\PayloadInterface;
use Ellipse\Handlers\ContainerResponder;
use Ellipse\Handlers\ResponderInterface;
use Ellipse\Handlers\Exceptions\ContainedResponderTypeException;

describe('ContainerResponder', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->responder = new ContainerResponder($this->container->get(), 'id');

    });

    it('should implement ResponderInterface', function () {

        expect($this->responder)->toBeAnInstanceOf(ResponderInterface::class);

    });

    describe('->response()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class);
            $this->payload = mock(PayloadInterface::class);

        });

        context('The value retrieved from the container implements ResponderInterface', function () {

            it('should proxy the ->response() method of the responder retrieved from the container', function () {

                $delegate = mock(ResponderInterface::class);
                $response = mock(ResponseInterface::class);

                $this->container->get->with('id')->returns($delegate);

                $delegate->response->with($this->request, $this->payload)->returns($response);

                $test = $this->responder->response($this->request->get(), $this->payload->get());

                expect($test)->toBe($response->get());

            });

        });

        context('The value retrieved from the container does not implement ResponderInterface', function () {

            it('should throw a ContainedResponderTypeException', function () {

                $this->container->get->with('id')->returns('responder');

                $test = function () {

                    $this->responder->response($this->request->get(), $this->payload->get());

                };

                $exception = new ContainedResponderTypeException('id', 'responder');

                expect($test)->toThrow($exception);

            });

        });

    });

});
