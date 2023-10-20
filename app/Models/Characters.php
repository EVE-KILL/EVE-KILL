<?php

namespace EK\Models;

use EK\Database\Collection;
use EK\Database\Connection;
use EK\EVE\ESI\Characters as ESICharacters;

class Characters extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'characters';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';

    /** @var string Primary index key */
    public string $indexField = 'characterID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = [];

    /** @var string[] $indexes The fields that should be indexed */
    public array $indexes = [
        'unique' => ['characterID'],
        'desc' => ['kills', 'losses', 'alliance_id', 'corporation_id'],
        'text' => ['name']
    ];

    public function __construct(
        protected Connection $connection,
        protected ESICharacters $esiCharacters
    ) {
        parent::__construct($connection);
    }

    public function getById(int $characterID, int $depth = 0): \Illuminate\Support\Collection
    {
        $result = $this->findOne(['characterID' => $characterID]);
        if ($result->isEmpty()) {
            $result = $this->esiCharacters->getCharacterInfo($characterID);
            if ($result !== null) {
                $this->setData($result->toArray());
                $this->save();
            }
        }

        return $result;
    }

}
