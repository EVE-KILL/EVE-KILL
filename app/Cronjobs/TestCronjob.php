<?php

namespace EK\Cronjobs;

use EK\Cron\Api\Cronjob;

class TestCronjob extends Cronjob
{
    protected string $cronTime = '*/5 * * * *';

    public function handle(): void
    {
        $this->logger->info('Hi mom');
    }
}
