<?php

namespace EK\Commands\Fetch;

use Composer\Autoload\ClassLoader;
use EK\Console\Api\ConsoleCommand;
use EK\Models\Killmails;
use Illuminate\Support\Collection;

class FetchKillmails extends ConsoleCommand
{
    public string $signature = 'fetch:killmails';
    public string $description = 'Fetch all the killmails available in the zKillboard History API';

    public function __construct(
        protected ClassLoader $autoloader,
        protected killmails $killmails,
    ) {
        parent::__construct();
    }


    final public function handle(): void
    {
        // Killmails processed
        $processed = 1;
        // Killmails in total
        $totalKillmails = 0;

        // Get the totals available from zKillboard
        $totalData = file_get_contents('https://zkillboard.com/api/history/totals.json') ?? [];
        $totalAvailable = new Collection(json_decode($totalData, true, flags: \JSON_THROW_ON_ERROR));

        (new Collection($totalAvailable))->transform(static function ($row) use (&$totalKillmails) {
            $totalKillmails += $row;
        });

        $this->out('Iterating over ' . count($totalAvailable) . ' individual days');
        foreach ($totalAvailable->reverse() as $date => $total) {
            $this->out("Day: {$date} | {$total} kills available");
            try {
                $file = \BASE_DIR . "/resources/cache/{$date}.json";
                if (!file_exists($file)) {
                    $this->out("Fetching from: https://zkillboard.com/api/history/{$date}.json");
                    $kills = file_get_contents("https://zkillboard.com/api/history/{$date}.json");
                    if (!empty($kills)) {
                        file_put_contents($file, $kills);
                        $kills = json_decode($kills, true);
                    } else {
                        throw new \RuntimeException('Kills was empty');
                    }
                } else {
                    $kills = json_decode(
                        file_get_contents($file),
                        true
                    );
                }
            } catch (\Exception $e) {
                sleep(10);
                $this->out("Fetching from: https://zkillboard.com/api/history/{$date}.json");
                $kills = json_decode(
                    file_get_contents("https://zkillboard.com/api/history/{$date}.json"),
                    true
                );
            }
            foreach ($kills as $killId => $hash) {
                if ($killId === 'day') {
                    continue;
                }

                if ($this->killmails->findOne(['killmail_id' => $killId])->isNotEmpty()) {
                    $this->out("{$date} / {$processed} / {$totalKillmails} | killID: {$killId} ({$hash}) | Already exists");
                    $processed++;
                    continue;
                }

                $this->killmails->clear();
                $this->killmails->setData([
                    'killmail_id' => $killId,
                    'hash' => $hash,
                    'fetched' => false,
                ]);
                try {
                    $this->killmails->save();
                } catch (\Exception $e) {
                    dd($e->getMessage());
                }

                $this->out("{$date} / {$processed} / {$totalKillmails} | killID: {$killId} ({$hash})");
                $processed++;
            }
        }
    }
}
