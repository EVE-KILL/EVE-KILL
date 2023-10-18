<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class GroupIDs extends SeedInterface
{
    public string $collectionName = 'groupids';
    public string $fileName = 'groupIDs.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\GroupIDs $groupids
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->groupids->setData(array_merge(['groupID' => $key], $data));
            $this->groupids->save();
        }
    }
}
