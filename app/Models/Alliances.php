<?php

namespace EK\Models;

use EK\Database\Collection;
use EK\Database\Connection;
use EK\EVE\ESI\Alliances as ESIAlliances;

class Alliances extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'alliances';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';

    /** @var string Primary index key */
    public string $indexField = 'allianceID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = [];

    /** @var string[] $indexes The fields that should be indexed */
    public array $indexes = [
        'unique' => ['allianceID'],
    ];

    public function __construct(
        protected Connection $connection,
        protected ESIAlliances $esiAlliances
    ) {
        parent::__construct($connection);
    }

    public function getById(int $allianceID): \Illuminate\Support\Collection
    {
        $result = $this->findOne(['allianceID' => $allianceID]);
        if($result->isEmpty()) {
            $result = $this->esiAlliances->getAllianceInfo($allianceID);
            $this->setData($result->toArray());
            $this->save();
        }

        return $result;
    }

}
