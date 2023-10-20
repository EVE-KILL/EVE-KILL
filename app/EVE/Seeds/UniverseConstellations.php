<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use EK\EVE\Helpers\Universe;
use EK\EVE\Models\UniverseConstellations as UniverseConstellationsModel;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

class UniverseConstellations extends SeedInterface
{
    public string $collectionName = 'constellations';
    public string $fileName = '';

    public function __construct(
        protected Container $container,
        protected UniverseConstellationsModel $constellations,
        protected Universe $universe
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        $locations = glob(\BASE_DIR . '/resources/cache/sde/fsd/universe/*/*/*/constellation.staticdata');
        foreach($locations as $location) {
            $data = Yaml::parseFile($location);
            $regionExp = array_values(array_slice(explode('/', $location), -4, 4, true));
            $regionData = Yaml::parseFile(\BASE_DIR . '/resources/cache/sde/fsd/universe/' . $regionExp[0] . '/' . $regionExp[1] . '/region.staticdata');
            $constellationData = Yaml::parseFile(\BASE_DIR . '/resources/cache/sde/fsd/universe/' . $regionExp[0] . '/' . $regionExp[1] . '/' . $regionExp[2] . '/constellation.staticdata');

            $data = array_merge([
                'regionID' => $regionData['regionID'],
                'regionName' => $this->universe->fixRegionNames($regionExp[1]),
                'constellationName' => $regionExp[2],
                'constellationID' => $constellationData['constellationID'],
            ], $data);

            $this->constellations->setData($data);
            $this->constellations->save();
            $progressBar->advance();
        }
    }

    public function getItemCount(): int
    {
        $locations = glob(\BASE_DIR . '/resources/cache/sde/fsd/universe/*/*/*/constellation.staticdata');
        return count($locations);
    }
}
