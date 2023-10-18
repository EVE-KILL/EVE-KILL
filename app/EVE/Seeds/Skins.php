<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class Skins extends SeedInterface
{
    public string $collectionName = 'skins';
    public string $fileName = 'skins.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\Skins $skins
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $data) {
            $progressBar->advance();
            $this->skins->setData($data);
            $this->skins->save();
        }
    }
}
