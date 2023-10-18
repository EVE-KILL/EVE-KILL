<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class SkinMaterials extends SeedInterface
{
    public string $collectionName = 'skinmaterials';
    public string $fileName = 'skinMaterials.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\SkinMaterials $skinmaterials
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->skinmaterials->setData(array_merge(['skinMaterialID' => $key], $data));
            $this->skinmaterials->save();
        }
    }
}
