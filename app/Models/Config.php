<?php

namespace EK\Models;

use EK\Database\Collection;

class Config extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'config';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';

    /** @var string Primary index key */
    public string $indexField = 'key';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['key', 'value'];

    /** @var string[] $indexes The fields that should be indexed */
    public array $indexes = [
        'unique' => [['key', 'value']]
    ];
}
