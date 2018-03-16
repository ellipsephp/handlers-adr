<?php

use Ellipse\Handlers\Exceptions\InputTypeException;

describe('InputTypeException', function () {

    it('should extend TypeError', function () {

        $test = new InputTypeException('input');

        expect($test)->toBeAnInstanceOf(TypeError::class);

    });

});
