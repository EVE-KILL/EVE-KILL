<?php

namespace EK\EVE\ESI;
use EK\EVE\Api\ESIInterface;

class Alliances extends ESIInterface
{
    protected string $esiEndpoint = 'alliances';

    public function getAllianceInfo(int $allianceId): array
    {
        return $this->fetch($allianceId);
    }
}
