<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class Factions extends SeedInterface
{
    public string $collectionName = 'factions';
    public string $fileName = 'factions.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\Factions $factions
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $data) {
            $progressBar->advance();

            // The unknown faction has the corporationID 0 - simply for the sake of consistency
            if ($data['nameID']['en'] === 'Unknown') {
                $data['corporationID'] = 0;
            }

            $this->factions->setData($data);
            $this->factions->save();
        }
    }
}
