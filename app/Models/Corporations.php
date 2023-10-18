<?php

namespace EK\Models;

use EK\Database\Collection;
use EK\Database\Connection;
use EK\EVE\ESI\Corporations as ESICorporations;

class Corporations extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'corporations';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';

    /** @var string Primary index key */
    public string $indexField = 'corporationID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = [];

    /** @var string[] $indexes The fields that should be indexed */
    public array $indexes = [
        'unique' => ['corporationID'],
    ];

    public function __construct(
        protected Connection $connection,
        protected ESICorporations $esiCorporations
    ) {
        parent::__construct($connection);
    }

    public function getById(int $corporationID): \Illuminate\Support\Collection
    {
        $result = $this->findOne(['corporationID' => $corporationID]);
        if ($result->isEmpty()) {
            $result = $this->esiCorporations->getCorporationInfo($corporationID);
            $this->setData($result->toArray());
            $this->save();
        }

        return $result;
    }

}
