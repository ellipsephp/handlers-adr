<?php

use function Eloquent\Phony\Kahlan\mock;
use function Eloquent\Phony\Kahlan\stub;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;
use Ellipse\Handlers\ADR\PayloadInterface;
use Ellipse\Handlers\Exceptions\InputTypeException;

describe('ActionRequestHandler', function () {

    beforeEach(function () {

        $this->domain = mock(DomainInterface::class);
        $this->responder = mock(ResponderInterface::class);
        $this->parser = stub();

        $this->handler = new ActionRequestHandler($this->domain->get(), $this->responder->get(), $this->parser);

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class)->get();
            $this->response = mock(ResponseInterface::class)->get();

        });

        context('when the value returned by the parser is an array', function () {

            it('should return the response produced by the responder with the payload produced by the domain', function () {

                $input = ['key' => 'value'];
                $payload = mock(PayloadInterface::class)->get();

                $this->parser->with($this->request)->returns($input);

                $this->domain->payload->with($input)->returns($payload);
                $this->responder->createResponse->with($this->request, $payload)->returns($this->response);

                $test = $this->handler->handle($this->request);

                expect($test)->toBe($this->response);

            });

        });

        context('when the value returned by the parser is not an array', function () {

            it('should throw a InputTypeException', function () {

                $this->parser->with($this->request)->returns('input');

                $test = function () {

                    $this->handler->handle($this->request);

                };

                $exception = new InputTypeException('input');

                expect($test)->toThrow($exception);

            });

        });

    });

});
