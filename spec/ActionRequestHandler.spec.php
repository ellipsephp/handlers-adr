<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Container\ContainerInterface;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Handlers\ADR\PayloadInterface;
use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;
use Ellipse\Handlers\Exceptions\ContainedDomainTypeException;
use Ellipse\Handlers\Exceptions\ContainedResponderTypeException;

describe('ActionRequestHandler', function () {

    beforeEach(function () {

        $this->container = mock(ContainerInterface::class);

        $this->handler = new ActionRequestHandler($this->container->get(), 'Domain', 'Responder', [
            'k1' => 'v1.default',
            'k2' => 'v2.default',
        ]);

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class);

            $this->request->getAttributes->returns(['k2' => 'v2.attr', 'k3' => 'v3.attr']);
            $this->request->getQueryParams->returns(['k3' => 'v3.query', 'k4' => 'v4.query']);
            $this->request->getParsedBody->returns(['k4' => 'v4.body', 'k5' => 'v5.body']);
            $this->request->getUploadedFiles->returns(['k5' => 'v5.files']);

        });

        context('when the domain retrieved from the container is an object implementing DomainInterface', function () {

            beforeEach(function () {

                $this->domain = mock(DomainInterface::class);

                $this->container->get->with('Domain')->returns($this->domain);

            });

            context('when the responder retrieved from the container is an object implementing ResponderInterface', function () {

                beforeEach(function () {

                    $this->responder = mock(ResponderInterface::class);

                    $this->container->get->with('Responder')->returns($this->responder);

                });

                it('should return a response by following the action domain responder pattern', function () {

                    $payload = mock(PayloadInterface::class)->get();
                    $response = mock(ResponseInterface::class)->get();

                    $input = [
                        'k1' => 'v1.default',
                        'k2' => 'v2.attr',
                        'k3' => 'v3.query',
                        'k4' => 'v4.body',
                        'k5' => 'v5.files',
                    ];

                    $this->domain->payload->with($input)->returns($payload);

                    $this->responder->response->with($this->request, $payload)->returns($response);

                    $test = $this->handler->handle($this->request->get());

                    expect($test)->toBe($response);

                });

            });

            context('when the responder retrieved from the container is not an object implementing ResponderInterface', function () {

                it('should throw a ContainedResponderTypeException', function () {

                    $this->container->get->with('Responder')->returns('responder');

                    $test = function () {

                        $this->handler->handle($this->request->get());

                    };

                    $exception = new ContainedResponderTypeException('Responder', 'responder');

                    expect($test)->toThrow($exception);

                });

            });

        });

        context('when the domain retrieved from the container is not an object implementing DomainInterface', function () {

            it('should throw a ContainedDomainTypeException', function () {

                $this->container->get->with('Domain')->returns('domain');

                $test = function () {

                    $this->handler->handle($this->request->get());

                };

                $exception = new ContainedDomainTypeException('Domain', 'domain');

                expect($test)->toThrow($exception);

            });

        });

    });

});
