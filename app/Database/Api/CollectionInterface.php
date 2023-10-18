<?php

namespace EK\Database\Api;

use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use MongoDB\BSON\UTCDateTime;
use MongoDB\DeleteResult;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use Traversable;

interface CollectionInterface
{
    public function find(
        array $filter = [],
        #[ArrayShape(['$match' => 'array', '$unwind' => 'array', '$group' => 'array', '$project' => 'array', '$sort' => 'array', '$limit' => 'array'])]
        array $options = [],
        bool $showHidden = false
    ): Collection;

    public function findOne(
        array $filter = [],
        #[ArrayShape(['$match' => 'array', '$unwind' => 'array', '$group' => 'array', '$project' => 'array', '$sort' => 'array', '$limit' => 'array'])]
        array $options = [],
        bool $showHidden = false
    ): Collection;

    public function aggregate(
        array $pipeline = [],
        #[ArrayShape(['$match' => 'array', '$unwind' => 'array', '$group' => 'array', '$project' => 'array', '$sort' => 'array', '$limit' => 'array'])]
        array $options = []
    ): Traversable;

    public function count(
        array $filter = [],
        #[ArrayShape(['$match' => 'array', '$unwind' => 'array', '$group' => 'array', '$project' => 'array', '$sort' => 'array', '$limit' => 'array'])]
        array $options = []
    ): int;

    public function delete(array $filter = []): DeleteResult;

    public function update(array $filter = [], array $update = []): UpdateResult;

    public function truncate(): void;

    public function setData(array $data = [], bool $clear = false): void;

    public function getData(): Collection;

    public function saveMany(): void;

    public function save(): UpdateResult|InsertOneResult;

    public function clear(array $data = []): self;

    public function makeTimeFromDateTime(string $dateTime): UTCDateTime;

    public function makeTimeFromUnixTime(int $unixTime): UTCDateTime;

    public function makeTime(string|int $time): UTCDateTime;
}
