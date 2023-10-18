<?php

namespace EK\EVE\Api;
use Illuminate\Support\Collection;

abstract class ESIInterface
{
    protected string $esiUrl = 'https://esi.evetech.net/latest/';
    protected string $esiEndpoint = '';
    protected string $userAgent = 'EVE-KILLBOARD/0.1 @ michael@karbowiak.dk';

    public function fetch(string $endpoint): Collection
    {
        $url = $this->esiUrl . $this->esiEndpoint . '/' . $endpoint . '/';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        $result = curl_exec($ch);
        curl_close($ch);

        return collect(json_decode($result, true, flags: \JSON_THROW_ON_ERROR));
    }
}
