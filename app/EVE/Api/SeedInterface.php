<?php

namespace EK\EVE\Api;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

abstract class SeedInterface
{
    public string $collectionName = '';
    public string $fileName = '';

    public function getData(): ?array
    {
        if(!empty($this->fileName)) {
            try {
                return Yaml::parseFile(\BASE_DIR . '/resources/cache/sde/fsd/' . $this->fileName);
            } catch (\Exception $e) {
                throw new \RuntimeException($e->getMessage());
            }
        }

        throw new \RuntimeException('No path provided');
    }

    public function getSqliteConnection(): \PDO
    {
        return new \PDO('sqlite:' . \BASE_DIR . '/resources/cache/sqlite-latest.sqlite');
    }

    public function getItemCount(): int
    {
        $data = $this->getData();
        return count($data);
    }

    abstract public function execute(ProgressBar $progressBar): void;
}
