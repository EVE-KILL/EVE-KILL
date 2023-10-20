<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use EK\EVE\Helpers\Universe;
use EK\EVE\Models\UniverseRegions as UniverseRegionsModel;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

class UniverseRegions extends SeedInterface
{
    public string $collectionName = 'regions';
    public string $fileName = '';

    public function __construct(
        protected Container $container,
        protected UniverseRegionsModel $regions,
        protected Universe $universe
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        $locations = glob(\BASE_DIR . '/resources/cache/sde/fsd/universe/*/*/region.staticdata');
        foreach($locations as $location) {
            $data = Yaml::parseFile($location);
            $regionExp = array_values(array_slice(explode('/', $location), -3, 3, true));
            $data = array_merge([
                'regionName' => $this->universe->fixRegionNames($regionExp[1])
            ], $data);

            $this->regions->setData($data);
            $this->regions->save();
            $progressBar->advance();
        }
    }

    public function getItemCount(): int
    {
        $locations = glob(\BASE_DIR . '/resources/cache/sde/fsd/universe/*/*/region.staticdata');
        return count($locations);
    }
}
