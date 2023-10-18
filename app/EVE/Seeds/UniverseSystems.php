<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Yaml\Yaml;

class UniverseSystems extends SeedInterface
{
    public string $collectionName = 'solarsystems';
    public string $fileName = '';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\UniverseSystems $solarsystems
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        $locations = glob(\BASE_DIR . '/resources/cache/sde/fsd/universe/*/*/*/*/solarsystem.staticdata');
        foreach($locations as $location) {
            $data = Yaml::parseFile($location);
            $locationExp = array_values(array_slice(explode('/', $location), -5, 5, true));
            $regionData = Yaml::parseFile(\BASE_DIR . '/resources/cache/sde/fsd/universe/' . $locationExp[0] . '/' . $locationExp[1] . '/region.staticdata');
            $constellationData = Yaml::parseFile(\BASE_DIR . '/resources/cache/sde/fsd/universe/' . $locationExp[0] . '/' . $locationExp[1] . '/' . $locationExp[2] . '/constellation.staticdata');

            $data = array_merge([
                'regionID' => $regionData['regionID'],
                'regionName' => $locationExp[1],
                'constellationID' => $constellationData['constellationID'],
                'constellationName' => $locationExp[2],
                'solarSystemName' => $locationExp[3],
            ], $data);

            $this->solarsystems->setData($data);
            $this->solarsystems->save();
            $progressBar->advance();
        }
    }

    public function getItemCount(): int
    {
        $locations = glob(\BASE_DIR . '/resources/cache/sde/fsd/universe/*/*/*/*/solarsystem.staticdata');
        return count($locations);
    }
}
