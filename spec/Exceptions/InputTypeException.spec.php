<?php

use Ellipse\Handlers\Exceptions\InputTypeException;
use Ellipse\Handlers\Exceptions\ADRRequestHandlerExceptionInterface;

describe('InputTypeException', function () {

    beforeEach(function () {

        $this->exception = new InputTypeException('input');

    });

    it('should implement ADRRequestHandlerExceptionInterface', function () {

        expect($this->exception)->toBeAnInstanceOf(ADRRequestHandlerExceptionInterface::class);

    });

    it('should extend TypeError', function () {

        expect($this->exception)->toBeAnInstanceOf(TypeError::class);

    });

});
