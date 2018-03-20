<?php

use Ellipse\Handlers\Exceptions\ContainedResponderTypeException;
use Ellipse\Handlers\Exceptions\ADRRequestHandlerExceptionInterface;

describe('ContainedResponderTypeException', function () {

    beforeEach(function () {

        $this->exception = new ContainedResponderTypeException('id', 'responder');

    });

    it('should implement ADRRequestHandlerExceptionInterface', function () {

        expect($this->exception)->toBeAnInstanceOf(ADRRequestHandlerExceptionInterface::class);

    });

    it('should extend TypeError', function () {

        expect($this->exception)->toBeAnInstanceOf(TypeError::class);

    });

});
