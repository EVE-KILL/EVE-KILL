<?php

namespace EK\Jobs;

use EK\Http\Api\Jobs;
use EK\Models\Users;

class TestJob extends Jobs
{
    protected bool $retry = true;
    protected int $retryAttempts = 0;
    protected int $retryDelay = 0;
    protected string $queue = 'default';

    public function __construct(protected Users $users)
    {
    }

    public function handle(string $payload): void
    {
        $payload = json_decode($payload, true);
        $users = $this->users->find(['email' => $payload['email'] ?? '']);
        echo "found {$users->count()} users with email {$payload['email']}";
    }
}
