<?php

namespace EK\Jobs;

use EK\Http\Api\Jobs;
use EK\Models\Killmails;

class PopulateEsiKillmail extends Jobs
{
    protected bool $retry = true;
    protected int $retryAttempts = 0;
    protected int $retryDelay = 0;
    protected string $queue = 'default';

    public function __construct(protected Killmails $killmails)
    {
    }

    public function handle(string $payload): void
    {
        $payload = json_decode($payload, true);
        $killId = $payload['killId'];
        $hash = $payload['hash'];

        $esiKillmail = \file_get_contents("https://esi.evetech.net/latest/killmails/{$killId}/{$hash}");
        $this->killmails->setData(json_decode($esiKillmail, true));
        $this->killmails->save();

        // Push to parser so it's properly parsed
    }
}
