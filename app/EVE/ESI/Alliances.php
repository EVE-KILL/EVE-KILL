<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;
use MongoDB\BSON\UTCDateTime;

class Alliances extends ESIInterface
{
    protected string $esiEndpoint = 'alliances';

    public function getAllianceInfo(int $allianceId): Collection
    {
        $allianceData = $this->fetch($allianceId);

        $alliance = [];
        $alliance['allianceID'] = (int) $allianceId;
        $alliance['ticker'] = $allianceData['ticker'];
        $alliance['allianceName'] = $allianceData['name'];
        $alliance['executorCorporationID'] = (int) $allianceData->get('executor_corporation_id', 0);
        $alliance['dateFounded'] = new UTCDateTime(strtotime($allianceData['date_founded']) * 1000);
        $alliance['creatorID'] = (int) $allianceData['creator_id'];
        $alliance['creatorCorporationID'] = (int) $allianceData['creator_corporation_id'];

        return collect($alliance);
    }
}
