<?php

namespace EK\EVE\Models;

use EK\Database\Collection;

class CategoryIDs extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'categoryids';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'ccp';

    /** @var string Primary index key */
    public string $indexField = 'categoryID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['categoryID'];

    public function getAllByCategoryId(int $categoryId)
    {
        return $this->find(['categoryID' => $categoryId]);
    }

    public function getAllByName(string $name)
    {
        return $this->find(['name.en' => $name]);
    }

    public function getAllByGermanName(string $name)
    {
        return $this->find(['name.de' => $name]);
    }

    public function getAllByFrenchName(string $name)
    {
        return $this->find(['name.fr' => $name]);
    }

    public function getAllByJapaneseName(string $name)
    {
        return $this->find(['name.ja' => $name]);
    }

    public function getAllByRussianName(string $name)
    {
        return $this->find(['name.ru' => $name]);
    }

    public function getAllByChineseName(string $name)
    {
        return $this->find(['name.zh' => $name]);
    }

}
