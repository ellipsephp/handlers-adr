<?php

use function Eloquent\Phony\Kahlan\mock;

use Psr\Http\Message\ServerRequestInterface;

use Ellipse\Handlers\ADR\DefaultRequestParser;
use Ellipse\Handlers\ADR\RequestParserInterface;

describe('DefaultRequestParser', function () {

    beforeEach(function () {

        $this->parser = new DefaultRequestParser;

    });

    it('should implement RequestParserInterface', function () {

        expect($this->parser)->toBeAnInstanceOf(RequestParserInterface::class);

    });

    describe('->input()', function () {

        it('should return the given request attributes merged with its query params, parsed body and uploaded files', function () {

            $request = mock(ServerRequestInterface::class);

            $request->getAttributes->returns(['k1' => 'v1.attr', 'k2' => 'v2.attr']);
            $request->getQueryParams->returns(['k2' => 'v2.query', 'k3' => 'v3.query']);
            $request->getParsedBody->returns(['k3' => 'v3.body', 'k4' => 'v4.body']);
            $request->getUploadedFiles->returns(['k4' => 'v4.files']);

            $test = $this->parser->input($request->get());

            expect($test)->toEqual([
                'k1' => 'v1.attr',
                'k2' => 'v2.query',
                'k3' => 'v3.body',
                'k4' => 'v4.files',
            ]);

        });

    });

});
