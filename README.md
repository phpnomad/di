# phpnomad/di

[![Latest Version](https://img.shields.io/packagist/v/phpnomad/di.svg)](https://packagist.org/packages/phpnomad/di) [![Total Downloads](https://img.shields.io/packagist/dt/phpnomad/di.svg)](https://packagist.org/packages/phpnomad/di) [![PHP Version](https://img.shields.io/packagist/php-v/phpnomad/di.svg)](https://packagist.org/packages/phpnomad/di) [![License](https://img.shields.io/packagist/l/phpnomad/di.svg)](https://packagist.org/packages/phpnomad/di)

`phpnomad/di` is the contract-only dependency injection package for [PHPNomad](https://phpnomad.com). It defines the interfaces a container has to satisfy (`InstanceProvider`, `HasBindings`, `CanSetContainer`), a `HasSettableContainer` trait for classes that need the container handed to them after construction, and a `DiException` type. That's the entire package. It has zero runtime dependencies.

The concrete container implementation lives in a separate package, [`phpnomad/di-container`](https://packagist.org/packages/phpnomad/di-container). This split exists so your code can depend on the contracts without forcing a specific container implementation on anyone downstream. You can swap the container for a different implementation without touching any of the classes that inject `InstanceProvider`. `phpnomad/di-container` is the standard implementation that ships with PHPNomad and has been running in production for years across [Siren](https://sirenaffiliates.com) and several other client systems.

## Installation

```bash
composer require phpnomad/di
```

Most applications also install `phpnomad/di-container`, which provides the concrete `Container` class that implements these interfaces:

```bash
composer require phpnomad/di-container
```

## Quick Start

Classes declare `InstanceProvider` as a constructor parameter and use it to resolve dependencies by class name at runtime. Here's a report builder that needs a datastore it doesn't hold as a direct property:

```php
<?php

namespace MyApp\Reporting;

use MyApp\Widgets\WidgetDatastore;
use PHPNomad\Di\Interfaces\InstanceProvider;

class ReportBuilder
{
    protected InstanceProvider $container;

    public function __construct(InstanceProvider $container)
    {
        $this->container = $container;
    }

    public function buildWidgetReport(int $widgetId): array
    {
        $datastore = $this->container->get(WidgetDatastore::class);
        $widget = $datastore->getById($widgetId);

        return [
            'id'    => $widget->getId(),
            'name'  => $widget->getName(),
            'total' => $widget->getTotal(),
        ];
    }
}
```

The container you inject only has to implement `InstanceProvider`. In a PHPNomad application that's typically `PHPNomad\Di\Container\Container` from `phpnomad/di-container`, but any implementation of the interface works.

## Key Concepts

- `InstanceProvider` is the resolution contract. `get(class-string $abstract)` returns an autowired instance, or throws `DiException` if resolution fails.
- `HasBindings` is the binding contract. `bind(concrete, abstract, ...abstracts)` maps one concrete class to one or more abstracts, and `bindFactory(abstract, callable)` binds an abstract to a factory callable.
- `CanSetContainer` is the setter-injection interface for classes that need a container handed to them after construction rather than as a constructor argument. The `HasSettableContainer` trait implements it and stores the provider as a protected property.
- `DiException` is thrown when the container cannot resolve a requested class.

## Documentation

Full PHPNomad documentation lives at [phpnomad.com](https://phpnomad.com), including the bootstrapping guide that shows how containers, initializers, and facades fit together in a full application.

## License

MIT, see [LICENSE.txt](LICENSE.txt) for the full text.
