<?php

namespace EK\EVE\ESI;
use EK\EVE\Api\ESIInterface;

class Characters extends ESIInterface
{
    protected string $esiEndpoint = 'characters';

    public function getCharacterInfo(int $characterId): array
    {
        return $this->fetch($characterId);
    }
}
