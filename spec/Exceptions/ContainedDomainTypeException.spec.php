<?php

use Ellipse\Handlers\Exceptions\ContainedDomainTypeException;
use Ellipse\Handlers\Exceptions\ADRRequestHandlerExceptionInterface;

describe('ContainedDomainTypeException', function () {

    beforeEach(function () {

        $this->exception = new ContainedDomainTypeException('id', 'domain');

    });

    it('should implement ADRRequestHandlerExceptionInterface', function () {

        expect($this->exception)->toBeAnInstanceOf(ADRRequestHandlerExceptionInterface::class);

    });

    it('should extend TypeError', function () {

        expect($this->exception)->toBeAnInstanceOf(TypeError::class);

    });

});
