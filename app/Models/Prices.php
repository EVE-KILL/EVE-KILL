<?php

namespace EK\Models;

use EK\Database\Collection;
use MongoDB\BSON\UTCDateTime;
use RuntimeException;

class Prices extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'prices';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';

    /** @var string Primary index key */
    public string $indexField = 'typeID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['typeID', 'average', 'highest', 'lowest', 'regionID', 'date'];

    /** @var string[] $indexes The fields that should be indexed */
    public array $indexes = [
        'unique' => [['typeID', 'date', 'regionID']],
        'desc' => ['regionID'],
        'asc' => [],
        'text' => []
    ];

    public function getPriceByTypeId(int $typeId, string $date = null): \Illuminate\Support\Collection
    {
        $date = $date === null ? new UTCDateTime(time() * 1000) : new UTCDateTime(strtotime($date) * 1000);
        $price = $this->findOne(['typeID' => $typeId, 'date' => $date]);

        if ($price->isEmpty()) {
            $price = collect($this->aggregate([
                ['$match' => ['typeID' => $typeId]],
                ['$sort' => ['date' => -1]],
                ['$limit' => 1]
            ])) ?? [];
        }

        if (empty($price)) {
            throw new RuntimeException('No price found for typeID: ' . $typeId);
        }

        return $price;
    }
}
