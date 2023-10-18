<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class CategoryIDs extends SeedInterface
{
    public string $collectionName = 'categoryids';
    public string $fileName = 'categoryIDs.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\CategoryIDs $categoryIDs
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData() as $key => $data) {
            $progressBar->advance();
            $this->categoryIDs->setData(array_merge(['categoryID' => $key], $data));
            $this->categoryIDs->save();
        }
    }
}
