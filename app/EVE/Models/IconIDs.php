<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class IconIDs extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'iconids';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'iconID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['iconID'];

    public function getAllByIconId(int $iconId)
    {
        return $this->find(['iconID' => $iconId]);
    }

}