<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Handlers\ADR\PayloadInterface;
use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;
use Ellipse\Handlers\ADR\RequestParserInterface;
use Ellipse\Handlers\ADR\DefaultRequestParser;

describe('ActionRequestHandler', function () {

    beforeEach(function () {

        $this->domain = mock(DomainInterface::class);
        $this->responder = mock(ResponderInterface::class);

    });

    it('should implement RequestHandlerInterface', function () {

        $test = new ActionRequestHandler($this->domain->get(), $this->responder->get());

        expect($test)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        beforeEach(function () {

            $this->request = mock(ServerRequestInterface::class)->get();
            $this->response = mock(ResponseInterface::class)->get();

            $this->payload = mock(PayloadInterface::class)->get();

        });

        context('when there is no request parser', function () {

            it('should use the input array produced by a default request parser', function () {

                $input = ['key' => 'value'];
                $parser = mock(DefaultRequestParser::class);

                allow(DefaultRequestParser::class)->toBe($parser->get());

                $handler = new ActionRequestHandler($this->domain->get(), $this->responder->get());

                $parser->input->with($this->request)->returns($input);

                $this->domain->payload->with($input)->returns($this->payload);

                $this->responder->response->with($this->request, $this->payload)->returns($this->response);

                $test = $handler->handle($this->request);

                expect($test)->toBe($this->response);

            });

        });

        context('when there is a request parser', function () {

            it('should use the input array produced by the request parser', function () {

                $input = ['key' => 'value'];
                $parser = mock(RequestParserInterface::class);

                $handler = new ActionRequestHandler($this->domain->get(), $this->responder->get(), $parser->get());

                $parser->input->with($this->request)->returns($input);

                $this->domain->payload->with($input)->returns($this->payload);

                $this->responder->response->with($this->request, $this->payload)->returns($this->response);

                $test = $handler->handle($this->request);

                expect($test)->toBe($this->response);

            });

        });

    });

});
