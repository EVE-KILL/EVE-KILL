<?php

namespace EK\Http\Api;

use Spiral\Goridge\RPC\RPC;
use Spiral\RoadRunner\Jobs\Jobs as RoadRunnerJobs;

abstract class Jobs
{
    protected bool $retry = false;
    protected int $retryAttempts = 0;
    protected int $retryDelay = 0;
    protected string $queue = 'default';

    public function enqueue(array $payload): void
    {
        $jobsWorker = new RoadRunnerJobs(RPC::create('tcp://127.0.0.1:6001'));
        $queue = $jobsWorker->connect($this->queue);
        $task = $queue->create(get_class($this), json_encode($payload))
            ->withHeader('requeue', (bool) $this->retry)
            ->withHeader('attempts', (int) $this->retryAttempts)
            ->withHeader('retryDelay', (int) $this->retryDelay);

        $queue->dispatch($task);
    }

    /**
     * @param string $payload Payload is a json encoded string
     */
    abstract public function handle(string $payload): void;
}
