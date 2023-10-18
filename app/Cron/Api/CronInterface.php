<?php

namespace EK\Cron\Api;

interface CronInterface
{
    public function handle(): void;
}
