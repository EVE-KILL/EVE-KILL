<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

class TypeIDs extends SeedInterface
{
    public string $collectionName = 'typeids';
    public string $fileName = 'typeIDs.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\TypeIDs $typeids
    ) {

    }

    public function getData(bool $useSymfonyYaml = true): ?array
    {
        $data = file_get_contents(\BASE_DIR . '/resources/cache/sde/fsd/' . $this->fileName);
        $data = str_ireplace("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n'", "'", $data);
        $data = str_ireplace("\n\n\n\n\n'", "'", $data);
        $data = str_ireplace("\n\n\n\n'", "'", $data);
        $data = str_ireplace("\n\n\n'", "'", $data);
        $data = str_ireplace("\n\n'", "'", $data);
        file_put_contents(\BASE_DIR . '/resources/cache/sde_typeid.yaml', $data);

        return Yaml::parseFile(\BASE_DIR . '/resources/cache/sde_typeid.yaml');
    }
    public function execute(ProgressBar $progressBar): void
    {
        foreach($this->getData(false) as $key => $data) {
            $progressBar->advance();
            $this->typeids->setData(array_merge(['typeID' => $key], $data));
            $this->typeids->save();
        }
    }
}
