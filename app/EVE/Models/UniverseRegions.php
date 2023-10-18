<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class UniverseRegions extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'regions';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'regionID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['regionID'];

    public function getAllByDescriptionID($descriptionID)
    {
        return $this->find(['descriptionID' => $descriptionID]);
    }

    public function getAllByFactionID($factionID)
    {
        return $this->find(['factionID' => $factionID]);
    }

    public function getAllByNameID($nameID)
    {
        return $this->find(['nameID' => $nameID]);
    }

    public function getAllByRegionID($regionID)
    {
        return $this->find(['regionID' => $regionID]);
    }

    public function getAllByRegionName($fieldName)
    {
        return $this->find(['regionName' => $fieldName]);
    }


}
