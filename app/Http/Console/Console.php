<?php

namespace EK\Http\Console;

use Composer\Autoload\ClassLoader;
use EK\Http\SlimFramework;
use League\Container\Container;
use Nyholm\Psr7\Factory\Psr17Factory;
use RuntimeException;
use Spiral\RoadRunner\Http\PSR7Worker;
use Throwable;

class Console
{
    public function __construct(
        protected Container $container,
        protected ClassLoader $autoloader
    ) {
    }

    public function initWeb(Container $container, ClassLoader $autoloader)
    {
        // Init psr17Factory
        $psr17Factory = new Psr17Factory();

        // Get slimFramework
        $slimFramework = new SlimFramework(
            $autoloader,
            $container,
            psr17Factory: new Psr17Factory()
        );

        // Get the Slim App
        $app = $slimFramework->getSlim();

        // Start the RoadRunner Worker
        $worker = \Spiral\RoadRunner\Worker::create();
        $worker = new PSR7Worker($worker, $psr17Factory, $psr17Factory, $psr17Factory);

        while ($request = $worker->waitRequest()) {
            try {
                $response = $app->handle($request);
                $worker->respond($response);
            } catch (Throwable $e) {
                $worker->getWorker()->error($e->getMessage());
            }
        }
    }

    public function initJobs(Container $container)
    {
        $consumer = new \Spiral\RoadRunner\Jobs\Consumer();
        $requeue = false;
        while ($task = $consumer->waitTask()) {
            try {
                $requeue = (bool) $task->getHeaderLine('requeue');
                $className = (string) $task->getName(); // The class name of the task
                $payload = $task->getPayload();

                if (empty($className)) {
                    throw new RuntimeException('Error, task class name is empty..');
                }

                // Get the job instance and handle it
                $instance = $container->get($className);
                $instance->handle($payload);
                $task->complete();
            } catch (Throwable $e) {
                $attempts = (int) $task->getHeaderLine('attempts') - 1;
                $retryDelay = (int) $task->getHeaderLine('retryDelay') * 2;
                $task
                    ->withHeader('attempt', $attempts)
                    ->withHeader('retry-delay', $retryDelay)
                    ->fail($e, $requeue);
            }
        }
    }
}
