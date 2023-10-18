<?php

namespace EK\Database;

use JetBrains\PhpStorm\ArrayShape;
use MongoDB\Client;

class Connection
{
    public function __construct(
        protected array $servers = [
            [
                'host' => '127.0.0.1',
                'port' => 27017
            ]
        ],
        #[ArrayShape(['options' => 'array', 'typeMap' => 'array', 'db' => 'string'])]
        protected array $options = [
            'options' => [
                'connectTimeoutMS' => 30000,
                'socketTimeoutMS' => 30000,
                'serverSelectionTimeoutMS' => 30000
            ],
            'typeMap' => [
                'root' => 'object',
                'document' => 'object',
                'array' => 'object',
            ],
            'db' => 'EK'
        ]
    ) {
    }

    public function getConnectionString(): string
    {
        $connectionString = 'mongodb://';

        foreach ($this->servers as $server) {
            $connectionString .= $server['host'] . ':' . $server['port'] . ',';
        }

        return rtrim($connectionString, ',');
    }

    public function getConnection(): Client
    {
        return new Client(
            $this->getConnectionString(),
            $this->options
        );
    }
}
