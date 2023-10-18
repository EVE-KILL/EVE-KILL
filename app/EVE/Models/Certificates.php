<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class Certificates extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'certificates';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'certificateID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['certificateID'];

    public function getAllByCertificateId(int $certificateId)
    {
        return $this->find(['certificateID' => $certificateId]);
    }

    public function getAllByGroupId(int $groupId)
    {
        return $this->find(['groupID' => $groupId]);
    }

    public function getAllByName(string $name)
    {
        return $this->find(['name' => $name]);
    }
}
