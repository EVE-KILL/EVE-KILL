<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class TournamentRuleSets extends SeedInterface
{
    public string $collectionName = 'tournamentrulesets';
    public string $fileName = 'tournamentRuleSets.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\TournamentRuleSets $tournamentrulesets
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $data) {
            $progressBar->advance();
            $this->tournamentrulesets->setData($data);
            $this->tournamentrulesets->save();
        }
    }
}
