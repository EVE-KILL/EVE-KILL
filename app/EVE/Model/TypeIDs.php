<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class TypeIDs extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'typeids';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'typeID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['typeID'];

    public function getAllByGroupID($groupID)
    {
        return $this->find(['groupID' => $groupID]);
    }

    public function getAllByName($fieldName)
    {
        return $this->findOne(['name.en' => $fieldName]);
    }

    public function getAllByTypeID($typeID)
    {
        return $this->findOne(['typeID' => $typeID]);
    }

    public function getAllByGermanName($fieldName)
    {
        return $this->findOne(['name.de' => $fieldName]);
    }

    public function getAllByEnglishName($fieldName)
    {
        return $this->findOne(['name.en' => $fieldName]);
    }

    public function getAllByFrenchName($fieldName)
    {
        return $this->findOne(['name.fr' => $fieldName]);
    }

    public function getAllByJapaneseName($fieldName)
    {
        return $this->findOne(['name.ja' => $fieldName]);
    }

    public function getAllByRussianName($fieldName)
    {
        return $this->findOne(['name.ru' => $fieldName]);
    }

    public function getAllByChineseName($fieldName)
    {
        return $this->findOne(['name.zh' => $fieldName]);
    }

}
