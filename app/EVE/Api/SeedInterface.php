<?php

namespace EK\EVE\Api;

use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

abstract class SeedInterface
{
    public string $collectionName = '';
    public string $fileName = '';

    public function __construct(
        protected Container $container
    ) {

    }

    public function getData(bool $useSymfonyYaml = true): ?array
    {
        if(!empty($this->fileName)) {
            try {
            if ($useSymfonyYaml === true) {
                return Yaml::parseFile(\BASE_DIR . '/resources/cache/sde/fsd/' . $this->fileName);
            }
            if ($useSymfonyYaml === false) {
                return yaml_parse_file(\BASE_DIR . '/resources/cache/sde/fsd/' . $this->fileName);
            }
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

    public function ensurePrimaryIndex(): void
    {
        // Get name of class
        $className = get_class($this);

        // Replace Seeds with Models
        $className = str_replace('Seeds', 'Models', $className);

        // @var \EK\Database\Collection $model
        $model = $this->container->get($className);

        try {
            $model->collection->createIndex([$model->indexField => 1], ['unique' => true]);
        } catch (\Exception $e) {
            // Do nothing
        }
    }

    abstract public function execute(ProgressBar $progressBar): void;
}
