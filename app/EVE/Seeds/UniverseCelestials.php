<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use EK\EVE\Helpers\Universe;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class UniverseCelestials extends SeedInterface
{
    public string $collectionName = 'celestials';
    public string $fileName = 'celestials.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\UniverseCelestials $celestials,
        protected Universe $universe
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        $sqlite = $this->getSqliteConnection();
        $stmt = $sqlite->query('SELECT
            `mapDenormalize`.`itemID` AS `itemID`,
            `mapDenormalize`.`itemName` AS `itemName`,
            `invTypes`.`typeName` AS `typeName`,
            `mapDenormalize`.`typeID` AS `typeID`,
            `mapSolarSystems`.`solarSystemName` AS `solarSystemName`,
            `mapDenormalize`.`solarSystemID` AS `solarSystemID`,
            `mapDenormalize`.`constellationID` AS `constellationID`,
            `mapDenormalize`.`regionID` AS `regionID`,
            `mapRegions`.`regionName` AS `regionName`,
            `mapDenormalize`.`orbitID` AS `orbitID`,
            `mapDenormalize`.`x` AS `x`,
            `mapDenormalize`.`y` AS `y`,
            `mapDenormalize`.`z` AS `z` from
            ((((`mapDenormalize`
                join `invTypes` on((`mapDenormalize`.`typeID` = `invTypes`.`typeID`)))
                join `mapSolarSystems` on((`mapSolarSystems`.`solarSystemID` = `mapDenormalize`.`solarSystemID`)))
                join `mapRegions` on((`mapDenormalize`.`regionID` = `mapRegions`.`regionID`)))
                join `mapConstellations` on((`mapDenormalize`.`constellationID` = `mapConstellations`.`constellationID`))
            )'
        );
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        $bigInsert = [];
        foreach($result as $celestial) {
            $bigInsert[] = [
                'itemID' => (int) $celestial['itemID'],
                'itemName' => $celestial['itemName'],
                'typeName' => $celestial['typeName'],
                'typeID' => (int) $celestial['typeID'],
                'solarSystemName' => $celestial['solarSystemName'],
                'solarSystemID' => (int) $celestial['solarSystemID'],
                'constellationID' => (int) $celestial['constellationID'],
                'regionID' => (int) $celestial['regionID'],
                'regionName' => $this->universe->fixRegionNames($celestial['regionName']),
                'orbitID' => (int) $celestial['orbitID'],
                'x' => (float) $celestial['x'],
                'y' => (float) $celestial['y'],
                'z' => (float) $celestial['z'],
            ];
            $progressBar->advance();
        }
        $this->celestials->setData($bigInsert);
        $this->celestials->saveMany();
    }

    public function getItemCount(): int
    {
        $sqlite = $this->getSqliteConnection();
        $stmt = $sqlite->query('SELECT
            `mapDenormalize`.`itemID` AS `itemID`,
            `mapDenormalize`.`itemName` AS `itemName`,
            `invTypes`.`typeName` AS `typeName`,
            `mapDenormalize`.`typeID` AS `typeID`,
            `mapSolarSystems`.`solarSystemName` AS `solarSystemName`,
            `mapDenormalize`.`solarSystemID` AS `solarSystemID`,
            `mapDenormalize`.`constellationID` AS `constellationID`,
            `mapDenormalize`.`regionID` AS `regionID`,
            `mapRegions`.`regionName` AS `regionName`,
            `mapDenormalize`.`orbitID` AS `orbitID`,
            `mapDenormalize`.`x` AS `x`,
            `mapDenormalize`.`y` AS `y`,
            `mapDenormalize`.`z` AS `z` from
            ((((`mapDenormalize`
                join `invTypes` on((`mapDenormalize`.`typeID` = `invTypes`.`typeID`)))
                join `mapSolarSystems` on((`mapSolarSystems`.`solarSystemID` = `mapDenormalize`.`solarSystemID`)))
                join `mapRegions` on((`mapDenormalize`.`regionID` = `mapRegions`.`regionID`)))
                join `mapConstellations` on((`mapDenormalize`.`constellationID` = `mapConstellations`.`constellationID`))
            )'
        );
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return count($result);
    }
}
