<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class UniverseSystems extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'solarsystems';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'solarSystemID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['solarSystemID'];

    public function getAllByConstellationID($constellationID)
    {
        return $this->find(['constellationID' => $constellationID]);
    }

    public function getAllByConstellationName($fieldName)
    {
        return $this->find(['constellationName' => $fieldName]);
    }

    public function getAllByCorridor($corridor)
    {
        return $this->find(['corridor' => $corridor]);
    }

    public function getAllByRegionID($regionID)
    {
        return $this->find(['regionID' => $regionID]);
    }

    public function getAllByRegionName($fieldName)
    {
        return $this->find(['regionName' => $fieldName]);
    }

    public function getAllBySolarSystemID($solarSystemID)
    {
        return $this->findOne(['solarSystemID' => $solarSystemID]);
    }

    public function getAllBySolarSystemName($fieldName)
    {
        return $this->find(['solarSystemName' => $fieldName]);
    }

    public function getAllBySolarSystemNameID($solarSystemNameID)
    {
        return $this->find(['solarSystemNameID' => $solarSystemNameID]);
    }

    public function getAllByStarId($starId)
    {
        return $this->find(['star.id' => $starId]);
    }

    public function getAllByStarTypeID($starTypeID)
    {
        return $this->find(['star.typeID' => $starTypeID]);
    }

    public function getAllBySunTypeID($sunTypeID)
    {
        return $this->find(['sunTypeID' => $sunTypeID]);
    }

}
