<?php

namespace EK\Cron\Api;

use EK\Cron\Api\CronInterface;
use Monolog\Logger;

abstract class Cronjob implements CronInterface
{
    /**
     * Cronjob time listed as: minutes, hours, day of month, month, day of week
     *
     * @var string
     */
    protected string $cronTime = '* * * * *';

    public function __construct(
        protected Logger $logger
    ) {
    }

    abstract public function handle(): void;

    public function getCronTime(): string
    {
        return $this->cronTime;
    }
}
