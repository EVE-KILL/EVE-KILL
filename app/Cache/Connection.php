<?php

namespace EK\Cache;

use Predis\Client;

class Connection
{
    protected Client $predisClient;

    public function __construct(
        protected array $config = [
            'host' => [
                'tcp://127.0.0.1'
            ],
            'options' => [
                //'replication' => 'sentinel',
                //'service' => 'EK',
                //'autodiscovery' => true,
                //'cluster' => 'predis',
                'parameters' => [
                    // 'password' => ''
                ]
            ]
        ]
    ) {
        $this->predisClient = new Client($this->config['host'], $this->config['options']);
    }

    public function getClient(): Client
    {
        return $this->predisClient;
    }
}
