<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class IconIDs extends SeedInterface
{
    public string $collectionName = 'iconids';
    public string $fileName = 'iconIDs.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\IconIDs $iconids
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->iconids->setData(array_merge(['iconID' => $key], $data));
            $this->iconids->save();
        }
    }
}
