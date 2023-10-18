<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;

class Corporations extends ESIInterface
{
    protected string $esiEndpoint = 'corporations';

    public function getCorporationInfo(int $corporationId): Collection
    {
        $corporationData = $this->fetch($corporationId);
        $corporationData['corporationID'] = $corporationId;

        return $corporationData;
    }
}
