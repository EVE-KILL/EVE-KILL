<?php

namespace EK\Commands\Updates;

use EK\Console\Api\ConsoleCommand;
use EK\Models\Prices;
use League\Csv\Reader;

class UpdatePrices extends ConsoleCommand
{
    protected string $signature = 'update:prices { --historic : Gets _ALL_ Prices going back to 2016 from the Historic everef dataset }';
    protected string $description = 'Updates the item prices';

    public function __construct(
        protected Prices $prices
    ) {
        parent::__construct();
    }
    final public function handle(): void
    {
        if ($this->historic === true) {
            $this->historicData();
            exit(0);
        }
    }

    protected function historicData(): void
    {
        $historicDataBlobs = [
            '2016' => 'https://data.everef.net/market-history/market-history-2016.tar.bz2',
            '2017' => 'https://data.everef.net/market-history/market-history-2017.tar.bz2',
            '2018' => 'https://data.everef.net/market-history/market-history-2018.tar.bz2',
            '2019' => 'https://data.everef.net/market-history/market-history-2019.tar.bz2',
            '2020' => 'https://data.everef.net/market-history/market-history-2020.tar.bz2',
            '2021' => 'https://data.everef.net/market-history/market-history-2021.tar.bz2',
        ];

        $historicHistoryByDay = [
            '2022' => 'https://data.everef.net/market-history/2022',
            '2023' => 'https://data.everef.net/market-history/2023',
        ];

        $cachePath = \BASE_DIR . '/resources/cache';

        // Download and unpack the historic blobs
        $this->out('<info>Downloading Historic Data</info>');
        foreach ($historicDataBlobs as $year => $url) {
            $this->out("Downloading {$year}");
            exec("curl --progress-bar -o {$cachePath}/{$year}.tar.bz2 {$url}");
            exec("mkdir -p {$cachePath}/markethistory/{$year}");
            $this->out("Unpacking {$year}");
            exec("tar -xjf {$cachePath}/{$year}.tar.bz2 -C {$cachePath}/markethistory/{$year}/");
        }

        // Download all the historic data by date into markethistory
        $this->out('<info>Downloading Historic Data by Date</info>');
        foreach ($historicHistoryByDay as $year => $baseUrl) {
            $startDate = "{$year}-01-01";
            $daysInAYear = 365;
            $increments = 0;
            exec("mkdir -p {$cachePath}/markethistory/{$year}");

            do {
                // Make the currentDate the $startDate + $increments days already iterated
                $currentDate = date('Y-m-d', strtotime($startDate . ' + ' . $increments . ' days'));

                $this->out("Downloading {$currentDate}");
                // https://data.everef.net/market-history/2022/market-history-2022-01-01.csv.bz2
                exec("curl --progress-bar -o {$cachePath}/{$currentDate}.csv.bz2 {$baseUrl}/market-history-{$currentDate}.csv.bz2");
                exec("bzip2 -d {$cachePath}/{$currentDate}.csv.bz2");
                exec("mv {$cachePath}/{$currentDate}.csv {$cachePath}/markethistory/{$year}/{$currentDate}.csv");

                // If the $currentDate is the same as the actual current date('Y-m-d H:i:s') then we can stop
                if ($currentDate === date('Y-m-d')) {
                    break;
                }
            } while($increments++ < $daysInAYear);
        }

        // Now that it's all downloaded, we can import it
        $this->out('<info>Importing Historic Data</info>');
        $years = array_merge(array_keys($historicDataBlobs), array_keys($historicHistoryByDay));

        foreach($years as $year) {
            $this->out("Importing {$year}");
            $csvs = glob("{$cachePath}/markethistory/{$year}/*.csv");
            foreach($csvs as $csv) {
                $this->out("Importing {$csv}");
                $reader = Reader::createFromPath($csv);
                $reader->setHeaderOffset(0);
                $records = $reader->getRecords();

                $bigInsert = [];
                foreach($records as $record) {
                    $bigInsert[] = [
                        'typeID' => $record['type_id'],
                        'average' => $record['average'],
                        'highest' => $record['highest'],
                        'lowest' => $record['lowest'],
                        'regionID' => $record['region_id'],
                        'date' => $record['date']
                    ];
                }
                $this->prices->collection->insertMany($bigInsert);
            }
        }
    }
}
