# Request handler ADR

This package provides a [Psr-15](https://www.php-fig.org/psr/psr-15/) request handler implementing the [ADR](https://github.com/pmjones/adr) pattern using a [Psr-11](https://www.php-fig.org/psr/psr-11/) container.

**Require** php >= 7.0

**Installation** `composer require ellipse/handlers-adr`

**Run tests** `./vendor/bin/kahlan`

- [Request handler using ADR pattern](#request-handler-using-adr-pattern)
- [Example using auto wiring](#example-using-auto-wiring)

## Request handler using ADR pattern

Using the [Action-Domain-Responder](https://github.com/pmjones/adr) pattern allows to properly separate domain logic and presentation logic when handling an incoming request. Here is how this package implements it:

The `Ellipse\Handlers\ActionRequestHandler` class implements `Psr\Http\Server\RequestHandlerInterface` and is used as a generic *Action*. It takes a Psr-11 container as constructor parameter as well as two container entry ids for *Domain* and *Responder* instances. An optional default input array can also be specified as last parameter. Using a container allows to instanciate only the *Domain* and *Responder* classes used by the *Action* actually handling the request, when multiple request handlers are matched by a [router](https://github.com/ellipsephp/router-fastroute) for example.

The input array is obtained by merging the default input array, request attributes, request query parameters, request parsed body parameters and uploaded files. They are merged in this order, meaning default parameters would be overridden by request attributes having the same keys, which would be overridden by query parameters, etc...

The *Domain* instance is retrieved from the container and must implement `Ellipse\Handlers\ADR\DomainInterface`. It defines a `->payload()` method taking the input array as parameter and returning an implementation of `Ellipse\Handlers\ADR\PayloadInterface`. An `Ellipse\Handlers\Exceptions\ContainedDomainTypeException` is thrown when the value retrieved from the container is not an object implementing `DomainInterface`.

`PayloadInterface` defines two methods: `->status()` returning the payload status as a string and `->data()` returning the payload data as an array. A default implementation is provided by the `Ellipse\Handlers\ADR\Payload` class taking status and data as constructor parameters.

The *Responder* instance is retrieved from the container and must implement `Ellipse\Handlers\ADR\ResponderInterface`. It defines a `->response()` method taking the incoming request and the payload produced by the *Domain* as parameter and returning a Psr-7 response. An `Ellipse\Handlers\Exceptions\ContainedResponderTypeException` is thrown when the value retrieved from the container is not an object implementing `ResponderInterface`.

```php
<?php

namespace App\Domain;

use Ellipse\Handlers\ADR\DomainInterface;

use Ellipse\Handlers\ADR\Payload;
use Ellipse\Handlers\ADR\PayloadInterface;

use App\SomeService;

// Domain classes must implement DomainInterface
class SomeDomain implements DomainInterface
{
    private $service;

    public function __construct(SomeService $service)
    {
        $this->service = $service;
    }

    // The ->payload() method takes an input array as parameter and returns an implementation of PayloadInterface.
    public function payload(array $input): PayloadInterface
    {
        // perform domain logic using $this->service ...

        // A default payload is available. It takes a status and an associative array.
        return new Payload('FOUND', [
            'entity' => $data,
        ]);
    }
}
```

```php
<?php

namespace App\Responder;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

use Ellipse\Handlers\ADR\PayloadInterface;
use Ellipse\Handlers\ADR\ResponderInterface;

use App\ResponseFactory;

// Responder classes must implement ResponderInterface
class SomeResponder implements DomainInterface
{
    private $factory;

    public function __construct(ResponseFactory $factory)
    {
        $this->factory = $factory;
    }

    // The ->response() method takes a ServerRequestInterface and a PayloadInterface as parameter and return a ResponseInterface.
    public function response(ServerRequestInterface $request, PayloadInterface $payload): ResponseInterface
    {
        // Request headers can be used to perform content negotiation.
        // ...

        // PayloadInterface defines two methods:
        // - The ->status() method returning the payload status
        // - The ->values() method returning the payload data
        if ($payload->status() === 'FOUND') {

            $data = $payload->data();

            return $this->factory->createFoundResponse('some_template', $data);

        }

        return $this->factory->createNotFoundResponse();
    }
}
```

```php
<?php

namespace App;

use SomePsr11Container;

use Ellipse\Handlers\ActionRequestHandler;

use App\Domain\SomeDomain;
use App\Responder\SomeResponder;

// Get some Psr-11 container.
$container = new SomePsr11Container;

// Register the domain in the container.
$container->set(SomeDomain::class, function ($container) {

    return new SomeDomain(new SomeService);

});

// Register the responder in the container.
$container->set(SomeResponder::class, function ($container) {

    return new SomeResponder(new ResponseFactory);

});

// Create an action request handler using SomeDomain and SomeResponder.
$handler = new ActionRequestHandler($container, SomeDomain::class, SomeResponder::class);

// An optional input array can be specified.
// Those values are overridden by the request data having the same keys.
$handler = new ActionRequestHandler($container, SomeDomain::class, SomeResponder::class, [
    'default1' => 'value1',
    'default2' => 'value2',
]);

// action request handler instances work like any Psr-15 request handler.
$response = $handler->handle($request);
```

## Example using auto wiring

It can be cumbersome to register every *Domain* and *Responder* classes in the container. Here is how to auto wire *Domain* and *Responder* classes using the `Ellipse\Container\ReflectionContainer` class from the [ellipse/container-reflection](https://github.com/ellipsephp/container-reflection) package.

```php
<?php

namespace App;

use SomePsr11Container;

use Ellipse\Container\ReflectionContainer;
use Ellipse\Handlers\ActionRequestHandler;
use Ellipse\Handlers\ADR\DomainInterface;
use Ellipse\Handlers\ADR\ResponderInterface;

use App\Domain\SomeDomain;
use App\Responder\SomeResponder;

// Get some Psr-11 container.
$container = new SomePsr11Container;

// Decorate the container with a reflection container.
// Specify the domain and responder classes can be auto wired.
$reflection = new ReflectionContainer($container, [
    DomainInterface::class,
    ResponderInterface::class,
]);

// Create an action request handler using the reflection container.
$handler = new ActionRequestHandler($reflection, SomeDomain::class, SomeResponder::class);

// Instances of SomeDomain and SomeResponder are built using auto wiring.
$response = $handler->handle($request);
```
