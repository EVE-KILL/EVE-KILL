<?php

namespace EK\EVE\ESI;

use EK\EVE\Api\ESIInterface;
use Illuminate\Support\Collection;
use MongoDB\BSON\UTCDateTime;

class Corporations extends ESIInterface
{
    protected string $esiEndpoint = 'corporations';

    public function getCorporationInfo(int $corporationId): Collection
    {
        $corporationData = $this->fetch($corporationId);

        $corporation = [];
        $corporation['corporationID'] = $corporationId;
        $corporation['corporationName'] = $corporationData['name'];
        $corporation['url'] = $corporationData['url'];
        $corporation['ticker'] = $corporationData['ticker'];
        $corporation['taxRate'] = (float) $corporationData['tax_rate'];
        $corporation['shares'] = (int) $corporationData['shares'];
        $corporation['memberCount'] = (int) $corporationData['member_count'];
        $corporation['homeStationID'] = (int) $corporationData['home_station_id'];
        $corporation['description'] = $corporationData['description'];
        $corporation['dateFounded'] = new UTCDateTime(strtotime(empty($corporationData['date_founded']) ? '2003-05-06 00:00:00' : $corporationData['date_founded']) * 1000);
        $corporation['creatorID'] = (int) $corporationData['creator_id'];
        $corporation['ceoID'] = (int) $corporationData['ceo_id'];

        return collect($corporation);
    }
}
