<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;

class Characters extends ESIInterface
{
    protected string $esiEndpoint = 'characters';

    public function getCharacterInfo(int $characterId): Collection
    {
        $characterData = $this->fetch($characterId);
        $characterData['characterID'] = $characterId;

        return $characterData;
    }
}
