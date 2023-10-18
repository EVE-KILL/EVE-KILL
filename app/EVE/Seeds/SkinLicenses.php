<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class SkinLicenses extends SeedInterface
{
    public string $collectionName = 'skinlicenses';
    public string $fileName = 'skinLicenses.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\SkinLicenses $skinLicenses
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $data) {
            $progressBar->advance();
            $this->skinLicenses->setData($data);
            $this->skinLicenses->save();
        }
    }
}
