<?php

use function Eloquent\Phony\Kahlan\stub;
use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\ADR\PayloadInterface;
use Ellipse\ADR\DomainInterface;
use Ellipse\Handlers\ResponderInterface;
use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Handlers\Exceptions\InputTypeException;

describe('ActionRequestHandler', function () {

    beforeEach(function () {

        $this->domain = mock(DomainInterface::class);
        $this->responder = mock(ResponderInterface::class);

        $this->handler = new ActionRequestHandler($this->domain->get(), $this->responder->get());

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class);

            $this->request->getAttributes->returns(['k1' => 'v1.attr', 'k2' => 'v2.attr']);
            $this->request->getQueryParams->returns(['k2' => 'v2.query', 'k3' => 'v3.query']);
            $this->request->getParsedBody->returns(['k3' => 'v3.body', 'k4' => 'v4.body']);
            $this->request->getUploadedFiles->returns(['k4' => 'v4.files']);

        });

        it('should return a response by following the action domain responder pattern', function () {

            $payload = mock(PayloadInterface::class)->get();
            $response = mock(ResponseInterface::class)->get();

            $this->domain->payload->returns($payload);

            $this->responder->response->with($this->request, $payload)->returns($response);

            $test = $this->handler->handle($this->request->get());

            expect($test)->toBe($response);

        });

        context('when no request parser is given', function () {

            it('should call the domain ->payload() method with a merged array of all request attributes', function () {

                $this->handler->handle($this->request->get());

                $this->domain->payload->once()->calledWith([
                    'k1' => 'v1.attr',
                    'k2' => 'v2.query',
                    'k3' => 'v3.body',
                    'k4' => 'v4.files',
                ]);

            });

        });

        context('when a request parser is given', function () {

            beforeEach(function () {

                $this->parser = stub();

                $this->handler = new ActionRequestHandler($this->domain->get(), $this->responder->get(), $this->parser);

            });

            context('when the request parser returns an array', function () {

                it('should call the domain ->payload() method with this array', function () {

                    $this->parser->with($this->request)->returns(['key' => 'value']);

                    $this->handler->handle($this->request->get());

                    $this->domain->payload->once()->calledWith(['key' => 'value']);

                });

            });

            context('when the request parser does not return an array', function () {

                it('should throw an InputTypeException', function () {

                    $this->parser->with($this->request)->returns('input');

                    $test = function () {

                        $this->handler->handle($this->request->get());

                    };

                    $exception = new InputTypeException('input');

                    expect($test)->toThrow($exception);

                });

            });

        });

    });

});
