<?php

namespace EK\Database;

use EK\Database\Api\CollectionInterface;
use EK\Database\Connection;
use Exception;
use Illuminate\Support\Collection as IlluminateCollection;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Client;
use MongoDB\DeleteResult;
use MongoDB\GridFS\Bucket;
use MongoDB\InsertOneResult;
use MongoDB\UpdateResult;
use Traversable;

class Collection implements CollectionInterface
{
    /** @var string Name of collection in database */
    public string $collectionName = '';
    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';
    /** @var \MongoDB\Collection MongoDB Collection */
    public \MongoDB\Collection $collection;
    /** @var Bucket MongoDB GridFS Bucket for storing files */
    public Bucket $bucket;
    /** @var string Primary index key */
    public string $indexField = '';
    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];
    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = [];
    /** @var \Illuminate\Support\Collection Data collection when storing data */
    protected IlluminateCollection $data;
    /** @var \MongoDB\Client MongoDB client connection */
    private Client $client;

    public function __construct(
        protected Connection $connection,
    ) {
        $this->client = $connection->getConnection();
        $this->collection = $this->client
            ->selectDatabase($this->databaseName)
            ->selectCollection($this->collectionName);

        $this->bucket = $this->client
            ->selectDatabase($this->databaseName)
            ->selectGridFSBucket();

        $this->data = new IlluminateCollection();
    }

    public function find(array $filter = [], array $options = [], bool $showHidden = false): IlluminateCollection
    {
        $result = $this->collection->find($filter, $options)->toArray();

        if ($showHidden) {
            return \collect($result);
        }

        return (collect($result))->forget($this->hiddenFields);
    }

    public function findOne(array $filter = [], array $options = [], bool $showHidden = false): IlluminateCollection
    {
        return collect($this->find($filter, $options, $showHidden)->first() ?? []);
    }

    public function aggregate(array $pipeline = [], array $options = []): Traversable
    {
        return $this->collection->aggregate($pipeline, $options);
    }

    public function count(array $filter = [], array $options = []): int
    {
        return $this->collection->countDocuments($filter, $options);
    }

    public function delete(array $filter = []): DeleteResult
    {
        if (empty($filter)) {
            throw new \Exception('Filter cannot be empty');
        }

        return $this->collection->deleteOne($filter);
    }

    public function update(array $filter = [], array $update = []): UpdateResult
    {
        if (empty($filter)) {
            throw new \Exception('Filter cannot be empty');
        }

        return $this->collection->updateOne($filter, $update);
    }

    public function truncate(): void
    {
        try {
            $this->collection->drop();
        } catch (\Exception $e) {
            throw new \Exception('Error truncating collection: ' . $e->getMessage());
        }
    }

    public function setData(array $data = [], bool $clear = false): void
    {
        if ($clear) {
            $this->data = new IlluminateCollection();
        }

        $this->data = $this->data->merge($data);
    }

    public function getData(): IlluminateCollection
    {
        return $this->data;
    }

    public function saveMany(): void
    {
        $this->collection->insertMany($this->data->all());
    }

    public function save(): UpdateResult|InsertOneResult
    {
        // Does it have the required fiels?
        $this->hasRequired();

        try {
            return $this->collection->updateOne(
                [$this->indexField => $this->data->get($this->indexField)],
                [
                    '$set' => $this->data->all(),
                    '$currentDate' => ['lastModified' => true],
                ],
                [
                    'upsert' => true
                ]
            );
        } catch (Exception $e) {
            throw new Exception('Error occurred during transaction: ' . $e->getMessage());
        }
    }

    public function clear(array $data = []): self
    {
        $this->data = new IlluminateCollection();
        if (!empty($data)) {
            $this->data = $this->data->merge($data);
        }

        return $this;
    }

    public function makeTimeFromDateTime(string $dateTime): UTCDateTime
    {
        return new UTCDateTime(strtotime($dateTime) * 1000);
    }

    public function makeTimeFromUnixTime(int $unixTime): UTCDateTime
    {
        return new UTCDateTime($unixTime * 1000);
    }

    public function makeTime(string|int $time): UTCDateTime
    {
        if (is_int($time)) {
            return $this->makeTimeFromUnixTime($time);
        }

        return $this->makeTimeFromDateTime($time);
    }

    /**
     * @return true
     */
    public function hasRequired(IlluminateCollection $data = null): bool
    {
        if (!empty($this->required)) {
            foreach ($this->required as $key) {
                if ($data !== null && !$data->has($key)) {
                    throw new Exception('Error: ' . $key . ' does not exist in data..');
                }
                if (!$this->data->has($key)) {
                    throw new Exception('Error: ' . $key . ' does not exist in data..');
                }
            }
        }

        return true;
    }

    public function createIndex(array $keys = [], array $options = []): void
    {
        $this->collection->createIndex($keys, $options);
    }

    public function dropIndex(array $keys = [], array $options = []): void
    {
        $this->collection->dropIndex($keys, $options);
    }

    public function dropIndexes(): void
    {
        $this->collection->dropIndexes();
    }

    public function listIndexes(): array
    {
        return $this->collection->listIndexes()->toArray();
    }
}
