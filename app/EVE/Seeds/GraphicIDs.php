<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class GraphicIDs extends SeedInterface
{
    public string $collectionName = 'graphicids';
    public string $fileName = 'graphicIDs.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\GraphicIDs $graphicids
    ) {

    }

    public function execute(?ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->graphicids->setData(array_merge(['graphicsID' => $key], $data));
            $this->graphicids->save();
        }
    }
}
