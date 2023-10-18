<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;

class Killmails extends ESIInterface
{
    protected string $esiEndpoint = 'killmails';

    public function getKillmail(int $killId, string $hash): Collection
    {
        return $this->fetch($killId . '/' . $hash);
    }
}
