<?php

use Ellipse\Handlers\ADR\Payload;
use Ellipse\Handlers\ADR\PayloadInterface;

describe('Payload', function () {

    it('should implement JsonSerializable', function () {

        $test = new Payload('status');

        expect($test)->toBeAnInstanceOf(JsonSerializable::class);

    });

    it('should implement PayloadInterface', function () {

        $test = new Payload('status');

        expect($test)->toBeAnInstanceOf(PayloadInterface::class);

    });

    describe('->status()', function () {

        it('should return the status', function () {

            $payload = new Payload('status');

            $test = $payload->status();

            expect($test)->toEqual('status');

        });

    });

    describe('->values()', function () {

        context('when the payload does not have values', function () {

            it('should return an empty array', function () {

                $payload = new Payload('status');

                $test = $payload->values();

                expect($test)->toEqual([]);

            });

        });

        context('when the payload does not have values', function () {

            it('should return the values', function () {

                $payload = new Payload('status', ['key' => 'value']);

                $test = $payload->values();

                expect($test)->toEqual(['key' => 'value']);

            });

        });

    });

    context('when json encoded', function () {

        context('when the payload does not have values', function () {

            it('should return a json string with the status and an empty array', function () {

                $payload = new Payload('status');

                $test = json_encode($payload);

                expect($test)->toEqual(json_encode([
                    'status' => 'status',
                    'values' => [],
                ]));

            });

        });

        context('when the payload does not have values', function () {

            it('should return the values', function () {

                $payload = new Payload('status', ['key' => 'value']);

                $test = json_encode($payload);

                expect($test)->toEqual(json_encode([
                    'status' => 'status',
                    'values' => ['key' => 'value'],
                ]));

            });

        });

    });

});
