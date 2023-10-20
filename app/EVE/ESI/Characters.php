<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;
use MongoDB\BSON\UTCDateTime;

class Characters extends ESIInterface
{
    protected string $esiEndpoint = 'characters';

    public function __construct(
        protected Corporations $corporations,
    ) {
        parent::__construct();
    }

    public function getCharacterInfo(int $characterId): Collection
    {
        $characterData = $this->fetch($characterId);

        if ($characterData->get('error') !== null) {
            return collect([
                'characterID' => $characterId,
                'characterName' => 'Character Has Been Deleted',
                'corporationID' => null,
                'corporationName' => null,
                'securityStatus' => null,
                'raceID' => null,
                'gender' => null,
                'description' => null,
                'bloodlineID' => null,
                'birthday' => null
            ]);
        }

        $character = [];
        $character['characterID'] = (int) $characterId;
        $character['characterName'] = $characterData['name'];
        $character['corporationID'] = (int) $characterData['corporation_id'];
        $character['corporationName'] = $this->corporations->getCorporationInfo($characterData['corporation_id'])['corporationName'];
        $character['securityStatus'] = (float) $characterData['security_status'];
        $character['raceID'] = (int) $characterData['race_id'];
        $character['gender'] = $characterData['gender'];
        $character['description'] = $characterData['description'];
        $character['bloodlineID'] = (int) $characterData['bloodline_id'];
        $character['birthday'] = new UTCDateTime(strtotime($characterData['birthday']) * 1000);

        return collect($character);
    }
}
