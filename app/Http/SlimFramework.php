<?php

namespace EK\Http;

use Composer\Autoload\ClassLoader;
use EK\Http\Attributes\RouteAttribute;
use Kcs\ClassFinder\Finder\ComposerFinder;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Slim\App;
use Slim\Factory\AppFactory;

class SlimFramework
{
    public function __construct(
        protected ClassLoader $autoloader,
        protected ContainerInterface $container,
        protected ?App $slim = null,
        protected ?Psr17Factory $psr17Factory = null,
        protected string $controllerNamespace = 'EK\\Controllers',
        protected string $middlewareNamespace = 'EK\\Middlewares',
    ) {
        // Init the PSR17 factory
        $this->psr17Factory = $psr17Factory ?? new Psr17Factory();

        // Init slim framework
        $this->slim = $slim ?? AppFactory::create($this->psr17Factory, $container);

        // Generate routes
        $this->generateRoutes();

        // Generate middlewares
        $this->generateMiddlewares();
    }

    public function getSlim(): App
    {
        return $this->slim;
    }

    private function generateRoutes(): void
    {
        $controllerFinder = new ComposerFinder($this->autoloader);
        $controllerFinder->inNamespace($this->controllerNamespace);

        /** @var ReflectionClass $reflection */
        foreach ($controllerFinder as $className => $reflection) {
            try {
                foreach ($reflection->getMethods() as $method) {
                    $attributes = $method->getAttributes(RouteAttribute::class);
                    foreach ($attributes as $attribute) {
                        $apiUrl = $attribute->newInstance();
                        $loaded = $this->container->get($className);

                        $this->slim->map($apiUrl->getType(), $apiUrl->getRoute(), $loaded($method->getName()));
                    }
                }
            } catch (\Throwable $e) {
                //
            }
        }
    }

    private function generateMiddlewares(): void
    {
        $middlewareFinder = new ComposerFinder($this->autoloader);
        $middlewareFinder->inNamespace($this->middlewareNamespace);

        /** @var ReflectionClass $reflection */
        foreach ($middlewareFinder as $className => $reflection) {
            try {
                $loaded = $this->container->get($className);
                $this->slim->addMiddleware($loaded);
            } catch (\Throwable $e) {
                //
            }
        }
    }
}
