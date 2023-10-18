<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class GroupIDs extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'groupids';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'groupID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['groupID'];

    public function getAllByCategoryID(int $categoryID)
    {
        return $this->find(['categoryID' => $categoryID]);
    }

    public function getAllByGroupID(int $groupID)
    {
        return $this->findOne(['groupID' => $groupID]);
    }

    public function getAllByName(string $fieldName)
    {
        return $this->find(['name.en' => $fieldName]);
    }

    public function getAllByGermanName(string $fieldName)
    {
        return $this->find(['name.de' => $fieldName]);
    }

    public function getAllByEnglishName(string $fieldName)
    {
        return $this->find(['name.en' => $fieldName]);
    }

    public function getAllByFrenchName(string $fieldName)
    {
        return $this->find(['name.fr' => $fieldName]);
    }

    public function getAllByJapaneseName(string $fieldName)
    {
        return $this->find(['name.ja' => $fieldName]);
    }

    public function getAllByRussianName(string $fieldName)
    {
        return $this->find(['name.ru' => $fieldName]);
    }

    public function getAllByChineseName(string $fieldName)
    {
        return $this->find(['name.zh' => $fieldName]);
    }

}
