<?php

namespace EK\EVE\Api;
use bandwidthThrottle\tokenBucket\BlockingConsumer;
use bandwidthThrottle\tokenBucket\Rate;
use bandwidthThrottle\tokenBucket\storage\FileStorage;
use bandwidthThrottle\tokenBucket\TokenBucket;
use Illuminate\Support\Collection;

abstract class ESIInterface
{
    protected string $esiUrl = 'https://esi.evetech.net/latest/';
    protected string $esiEndpoint = '';
    protected string $userAgent = 'EVE-KILL/0.1 @ michael@karbowiak.dk';
    protected string $bucketLocation = \BASE_DIR . '/resources/cache/token.bucket';
    protected BlockingConsumer $tokenBucket;

    public function __construct()
    {
        // Token bucket
        $storage = new FileStorage($this->bucketLocation);
        $reqs = 100;
        $rate = new Rate($reqs, Rate::SECOND);
        $bucket = new TokenBucket($reqs, $rate, $storage);
        $bucket->bootstrap($reqs);
        $this->tokenBucket = new BlockingConsumer($bucket);
    }

    public function fetch(string $endpoint): Collection
    {
        try {
            $this->tokenBucket->consume(1);
            $status = 0;
            $iterations = 0;
            do {
                $result = $this->getData($endpoint);
                $status = $result['statusCode'];
                if (in_array($status, [200, 404])) {
                    return collect($result['result']);
                }
                sleep(1);
                $iterations++;
            } while(in_array($status, [200, 404]) && $iterations < 5);

            throw new \Exception('Failed to fetch data from ESI: ' . $this->esiUrl . $this->esiEndpoint . '/' . $endpoint . '/');
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function getData(string $endpoint): array
    {
        $url = $this->esiUrl . $this->esiEndpoint . '/' . $endpoint . '/';

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, $this->userAgent);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = collect(json_decode($result, true, flags: \JSON_THROW_ON_ERROR));
        return [
            'statusCode' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'result' => $result
        ];
    }
}
