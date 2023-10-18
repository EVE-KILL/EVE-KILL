<?php

namespace EK\EVE\ESI;
use EK\EVE\Api\ESIInterface;

class Corporations extends ESIInterface
{
    protected string $esiEndpoint = 'corporations';

    public function getCorporationInfo(int $corporationId): array
    {
        return $this->fetch($corporationId);
    }
}
