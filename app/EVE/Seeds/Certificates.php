<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class Certificates extends SeedInterface
{
    public string $collectionName = 'certificates';
    public string $fileName = 'certificates.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\Certificates $certificates
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->certificates->setData(array_merge(['certificateID' => $key], $data));
            $this->certificates->save();
        }
    }
}
