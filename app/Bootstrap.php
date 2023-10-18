<?php

namespace EK;

use Composer\Autoload\ClassLoader;
use League\Container\Container;
use League\Container\ReflectionContainer;
use \Monolog\Logger;

class Bootstrap
{
    public function __construct(
        protected ClassLoader $autoloader,
        public ?Container $container = null
    ) {
        $this->buildContainer();
        $this->loggerInit();
        $this->databaseInit();
        $this->redisInit();
    }

    public function buildContainer(): void
    {
        // Instantiate a new container if we're not given one
        $this->container = $this->container ?? new Container();

        // Register the ReflectionContainer so autowiring works
        $this->container->delegate(
            new ReflectionContainer()
        );

        // Add the autoloader into the container
        $this->container->add(ClassLoader::class, $this->autoloader);

        // Add the container itself to itself..
        $this->container->add(Container::class, $this->container);
    }

    public function getContainer(): Container
    {
        return $this->container;
    }

    private function loggerInit(): void
    {
        $this->container->add(Logger::class, function () {
            $outputFormat = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";
            $formatter = new \Monolog\Formatter\LineFormatter($outputFormat);

            $streamHandler = new \Monolog\Handler\StreamHandler('php://stdout', Logger::DEBUG);
            $streamHandler->setFormatter($formatter);

            $logger = new Logger('EK');
            $logger->pushHandler($streamHandler);

            return $logger;
        });
    }

    private function databaseInit(): void
    {
        $this->container
            ->add(\EK\Database\Connection::class)
            ->addArgument([
                [
                    'host' => '127.0.0.1',
                    'port' => 27017
                ]
            ])
            ->addArgument([
                'options' => [
                    'connectTimeoutMS' => 30000,
                    'socketTimeoutMS' => 30000,
                    'serverSelectionTimeoutMS' => 30000
                ],
                'typeMap' => [
                    'root' => 'object',
                    'document' => 'object',
                    'array' => 'object',
                ],
                'db' => 'EK'
            ]);
    }

    private function redisInit(): void
    {
        $this->container
            ->add(\EK\Cache\Connection::class)
            ->addArgument(
                [
                'host' => [
                    'tcp://redis-master.EK.svc.cluster.local'
                ],
                'options' => [
                    'replication' => 'sentinel',
                    'service' => 'EK',
                    'autodiscovery' => true,
                    'cluster' => 'predis',
                    'parameters' => [
                            // 'password' => ''
                    ]
                ]
            ]
            );
    }
}
