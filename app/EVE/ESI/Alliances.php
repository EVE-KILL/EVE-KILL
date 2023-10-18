<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;

class Alliances extends ESIInterface
{
    protected string $esiEndpoint = 'alliances';

    public function getAllianceInfo(int $allianceId): Collection
    {
        $allianceData = $this->fetch($allianceId);
        $allianceData['allianceID'] = $allianceId;

        return $allianceData;
    }
}
