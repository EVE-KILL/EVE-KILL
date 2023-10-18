<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class Landmarks extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'landmarks';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'landmarkID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['landmarkID'];

    public function getAllByDescriptionId(int $descriptionId)
    {
        return $this->find(['descriptionID' => $descriptionId]);
    }

    public function getAllByLandmarkId(int $landmarkId)
    {
        return $this->find(['landmarkID' => $landmarkId]);
    }

    public function getAllByLandmarkNameId(int $landmarkNameId)
    {
        return $this->find(['landmarkNameId' => $landmarkNameId]);
    }

}
