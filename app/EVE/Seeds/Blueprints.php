<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use Symfony\Component\Console\Helper\ProgressBar;

class Blueprints extends SeedInterface
{
    public string $collectionName = 'blueprints';
    public string $fileName = 'blueprints.yaml';

    public function __construct(
        protected \EK\EVE\Models\Blueprints $blueprints
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $data) {
            $progressBar->advance();
            $this->blueprints->setData($data);
            $this->blueprints->save();
        }
    }
}
