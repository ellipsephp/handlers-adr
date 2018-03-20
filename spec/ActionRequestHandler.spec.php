<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;

use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Handlers\ADR\PayloadInterface;
use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;

describe('ActionRequestHandler', function () {

    beforeEach(function () {

        $this->domain = mock(DomainInterface::class);
        $this->responder = mock(ResponderInterface::class);

        $this->handler = new ActionRequestHandler($this->domain->get(), $this->responder->get(), [
            'k1' => 'v1.default',
            'k2' => 'v2.default',
        ]);

    });

    it('should implement RequestHandlerInterface', function () {

        expect($this->handler)->toBeAnInstanceOf(RequestHandlerInterface::class);

    });

    describe('->handle()', function () {

        it('should create an input from the request, use the domain to get a payload and the responder to get a response', function () {

            $request = mock(ServerRequestInterface::class);
            $response = mock(ResponseInterface::class)->get();

            $payload = mock(PayloadInterface::class)->get();

            $input = [
                'k1' => 'v1.default',
                'k2' => 'v2.attr',
                'k3' => 'v3.query',
                'k4' => 'v4.body',
                'k5' => 'v5.files',
            ];

            $request->getAttributes->returns(['k2' => 'v2.attr', 'k3' => 'v3.attr']);
            $request->getQueryParams->returns(['k3' => 'v3.query', 'k4' => 'v4.query']);
            $request->getParsedBody->returns(['k4' => 'v4.body', 'k5' => 'v5.body']);
            $request->getUploadedFiles->returns(['k5' => 'v5.files']);

            $this->domain->payload->with($input)->returns($payload);

            $this->responder->response->with($request, $payload)->returns($response);

            $test = $this->handler->handle($request->get());

            expect($test)->toBe($response);

        });

    });

});
