# Request handler ADR

This package provides a [Psr-15](https://www.php-fig.org/psr/psr-15/) request handler implementing the [Action-Domain-Responder](https://github.com/pmjones/adr) pattern.

**Require** php >= 7.0

**Installation** `composer require ellipse/handlers-adr`

**Run tests** `./vendor/bin/kahlan`

- [Request handler using ADR pattern](#request-handler-using-adr-pattern)
- [Usage with a Psr-11 container](#usage-with-a-psr-11-container)

## Request handler using ADR pattern

The [Action-Domain-Responder](https://github.com/pmjones/adr) (ADR) pattern is used to separate domain and presentation logic of an application. It can be summed up as having *Action* objects gluing together pairs of *Domain* and *Responder* objects in order to produce a response from an incoming request. An *Action* can therefore be considered as a Psr-15 request handler and the goal of this package is to provide an ADR implementation usable with any Psr-15 dispatching system.

The `Ellipse\Handlers\ActionRequestHandler` represents a generic *Action* implementing `Psr\Http\Server\RequestHandlerInterface`. Its first constructor parameter is a *Domain* object implementing `Ellipse\ADR\DomainInterface` and the second one is a *Responder* object implementing `Ellipse\Handler\ResponderInterface`. Here is what's going on when the `->handle()` method of an `ActionRequestHandler` instance is called with a Psr-7 request:

- An input array is extracted from the Psr-7 request
- A payload is produced by calling the `->payload()` method of the *Domain* with the input array
- A Psr-7 response is produced by calling the `->response()` method of the *Responder* with the Psr-7 request and the payload
- The Psr-7 response is returned

By default the input array is obtained by merging the request attributes, query parameters, parsed body parameters and uploaded files. They are merged in this order, meaning request attributes are overridden by query parameters having the same keys, which in turn are overridden by parsed body parameters, and finally by uploaded files. An *Action* specific request parsing logic can be specified by passing a callable as `ActionRequestHandler` third constructor parameter. This request parser callable is executed with the request as parameter and must return an array. An `Ellipse\Handlers\Exceptions\InputTypeException` is thrown when anything else than an array is returned.

`DomainInterface` defines a `->payload()` method taking an input array as parameter and returning an implementation of `Ellipse\ADR\PayloadInterface`.

`PayloadInterface` defines two methods: `->status()` returning the payload status as a string and `->data()` returning the payload data as an array. The `Ellipse\ADR\Payload` class can be used as a default implementation of `PayloadInterface`. It takes the status string and the data array as constructor parameters.

Finally, `ResponseInterface` defines a `->response()` method taking a request and an implementation of `PayloadInterface` as parameter and returning a response.

```php
<?php

namespace App\Domain;

use Ellipse\ADR\Payload;
use Ellipse\ADR\PayloadInterface;
use Ellipse\ADR\DomainInterface;

use App\SomeService;

class SomeDomain implements DomainInterface
{
    private $service;

    public function __construct(SomeService $service)
    {
        $this->service = $service;
    }

    public function payload(array $input): PayloadInterface
    {
        // perform domain logic...

        // This payload will be passed to the responder ->response() method.
        return new Payload('FOUND', ['k1' => $v1, 'k2' => $v2]);
    }
}
```

```php
<?php

namespace App\Responder;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\ADR\PayloadInterface;
use Ellipse\Handlers\ResponderInterface;

use App\ResponseFactory;

class SomeResponder implements ResponderInterface
{
    private $factory;

    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    public function response(ServerRequestInterface $request, PayloadInterface $payload): ResponseInterface
    {
        // Different Psr-7 responses can be produced according to the given Psr-7 request and
        // the given payload.

        if ($payload->status() === 'FOUND') {

            return $this->factory->createFoundResponse('template', $payload->data());

        }

        return $this->factory->createNotFoundResponse();
    }
}
```

```php
<?php

namespace App;

use App\Domain\SomeDomain;
use App\Responder\SomeResponder;

use Ellipse\Handlers\ActionRequestHandler;

// Create an action request handler using SomeDomain and SomeResponder.
$domain = new SomeSomain(new SomeService);
$responder = new SomeResponder(new ResponseFactory);

$handler = new ActionRequestHandler($domain, $responder);

// A specific request parsing callable can be specified.
$handler = new ActionRequestHandler($domain, $responder, function ($request) {

    $attributes = $request->getAttributes();

    return [
        'key' => explode(' ', $attributes['key']),
    ];

});

// Action request handler instances work like any Psr-15 request handler.
$response = $handler->handle($request);
```

## Usage with a Psr-11 container

In real world applications *Domain* and *Responder* instances are usually retrieved from a container.

This packages provides implementations of `DomainInterface` and `ResponderInterface` proxying a [Psr-11](https://www.php-fig.org/psr/psr-11/) container entry.

`Ellipse\Handlers\ContainerDomain` class takes a container and the container id of a *Domain* object as constructor parameters. When its `->payload()` method is called, the *Domain* is retrieved from the container and the payload produced by its `->payload()` method is returned. An `Ellipse\Handlers\Exceptions\ContainedDomainTypeException` is thrown when the container entry is not an implementation of `DomainInterface`.

In the same way `Ellipse\Handlers\ContainerResponder` class takes a container and the container id of a *Responder* object as constructor parameters. The container entry `->response()` method is proxied and an `Ellipse\Handlers\Exceptions\ContainedResponderTypeException` is thrown when it is not an implementation of `ResponderInterface`.

Finally, request parsing callables can also be retrieved from the container using the `Ellipse\Handlers\ContainerRequestParser` class with a container and the container id of a callable as constructor parameters. An `Ellipse\Handlers\Exceptions\ContainedRequestParserTypeException` is thrown when the container entry is not a callable.

```php
<?php

namespace App;

use SomePsr11Container;

use App\Domain\SomeDomain;
use App\Responder\SomeResponder;

use Ellipse\Handlers\ContainerDomain;
use Ellipse\Handlers\ContainerResponder;
use Ellipse\Handlers\ContainerRequestParser;
use Ellipse\Handlers\ActionRequestHandler;

// Register SomeDomain, SomeResponder and a request parser into a Psr-11 container.
$container = new SomePsr11Container;

$container->set(SomeDomain::class, function ($container) {

    $service = $container->get(SomeService::class);

    return new SomeDomain($service);

});

$container->set(SomeResponder::class, function ($container) {

    $factory = $container->get(ResponseFactory::class);

    return new SomeResponder($factory);

});

$container->set('adr.parser', function () {

    return function ($request) {

        $attributes = $request->getAttributes();

        return [
            'key' => explode(' ', $attributes['key']),
        ];

    };

});

// Create an action using domain, responder and request parser proxying container entries.
$domain = new ContainerDomain($container, SomeDomain::class);
$responder = new ContainerResponder($container, SomeResponder::class);
$parser = new ContainerRequestParser($container, 'adr.parser');

$handler = new ActionRequestHandler($domain, $responder, $parser);

// Actual domain, responder and request parser are retrieved from the container when the request is handled
// by the action.
$response = $handler->handle($request);
```

Of course *Domain* and *Responder* classes can be auto wired using `Ellipse\Container\ReflectionContainer` class from the [ellipse/container-reflection](https://github.com/ellipsephp/container-reflection) package.

```php
<?php

namespace App;

use SomePsr11Container;

use App\Domain\SomeDomain;
use App\Responder\SomeResponder;

use Ellipse\ADR\DomainInterface;
use Ellipse\Handlers\ResponderInterface;
use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Container\ReflectionContainer;

// Get some Psr-11 container.
$container = new SomePsr11Container;

// Decorate the container with a reflection container.
// Specify the domain and responder implementations can be auto wired.
$reflection = new ReflectionContainer($container, [
    DomainInterface::class,
    ResponderInterface::class,
]);

// Create an action using domain and responder proxying container entries.
$domain = new ContainerDomain($reflection, SomeDomain::class);
$responder = new ContainerResponder($reflection, SomeResponder::class);

$handler = new ActionRequestHandler($domain, $responder);

// Instances of SomeDomain and SomeResponder are built using auto wiring.
$response = $handler->handle($request);
```
