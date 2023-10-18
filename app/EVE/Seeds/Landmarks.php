<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class Landmarks extends SeedInterface
{
    public string $collectionName = 'landmarks';
    public string $fileName = 'landmarks/landmarks.staticdata';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\Landmarks $landmarks
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->landmarks->setData(array_merge(['landmarkID' => $key], $data));
            $this->landmarks->save();
        }
    }
}
