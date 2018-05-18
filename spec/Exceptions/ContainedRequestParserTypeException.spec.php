<?php

use Ellipse\Handlers\Exceptions\ContainedRequestParserTypeException;
use Ellipse\Handlers\Exceptions\ADRRequestHandlerExceptionInterface;

describe('ContainedRequestParserTypeException', function () {

    beforeEach(function () {

        $this->exception = new ContainedRequestParserTypeException('id', 'parser');

    });

    it('should implement ADRRequestHandlerExceptionInterface', function () {

        expect($this->exception)->toBeAnInstanceOf(ADRRequestHandlerExceptionInterface::class);

    });

    it('should extend TypeError', function () {

        expect($this->exception)->toBeAnInstanceOf(TypeError::class);

    });

});
