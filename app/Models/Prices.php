<?php

namespace EK\Models;

use EK\Database\Collection;
use MongoDB\BSON\UTCDateTime;
use RuntimeException;

class Prices extends Collection
{
    /** @var string Name of collection in database */
    public string $collectionName = 'prices';

    /** @var string Name of database that the collection is stored in */
    public string $databaseName = 'app';

    /** @var string Primary index key */
    public string $indexField = 'typeID';

    /** @var string[] $hiddenFields Fields to hide from output (ie. Password hash, email etc.) */
    public array $hiddenFields = [];

    /** @var string[] $required Fields required to insert data to model (ie. email, password hash, etc.) */
    public array $required = ['typeID', 'average', 'highest', 'lowest', 'regionID', 'date'];

    /** @var string[] $indexes The fields that should be indexed */
    public array $indexes = [
        'unique' => [['typeID', 'date', 'regionID']],
        'desc' => ['regionID'],
        'asc' => [],
        'text' => []
    ];

    public function getPriceByTypeId(int $typeId, string $date = null): float
    {
        $date = $date === null ? new UTCDateTime(time() * 1000) : new UTCDateTime(strtotime($date) * 1000);
        $price = $this->findOne(['typeID' => $typeId, 'date' => $date]);

        if ($price->isEmpty()) {
            $price = collect($this->aggregate([
                ['$match' => ['typeID' => $typeId]],
                ['$sort' => ['date' => -1]],
                ['$limit' => 1]
            ])) ?? [];
        }

        if ($price->isEmpty()) {
            $price = collect(['lowest' => $this->getCustomPrice($typeId, $date)]);
        }

        if ($price->isEmpty()) {
            throw new RuntimeException('No price found for typeID: ' . $typeId);
        }

        return $price->get('lowest') ?? 0;
    }

    private function getCustomPrice(int $typeId, string $date): float
    {
        switch ($typeId) {
            case 12478: // Khumaak
            case 34559: // Conflux Element
                return 0.01; // Items that get market manipulated and abused will go here
            case 44265: // Victory Firework
                return 0.01; // Items that drop from sites will go here

            // Items that have been determined to be obnoxiously market
            // manipulated will go here
            case 34558:
            case 34556:
            case 34560:
            case 36902:
            case 34557:
            case 44264:
                return 0.01;
            case 42243: // Chemosh
                return 70000000000;
            case 2834: // Utu
            case 3516: // Malice
            case 11375: // Freki
                return 80000000000; // 80b
            case 3518: // Vangel
            case 3514: // Revenant
            case 32788: // Cambion
            case 32790: // Etana
            case 32209: // Mimir
            case 11942: // Silver Magnate
            case 33673: // Whiptail
                return 100000000000; // 100b
            case 35779: // Imp
            case 42125: // Vendetta
            case 42246: // Caedes
            case 74141: // Geri
                return 120000000000; // 120b
            case 2836: // Adrestia
            case 33675: // Chameleon
            case 35781: // Fiend
            case 45530: // Virtuoso
            case 48636: // Hydra
            case 60765: // Raiju
            case 74316: // Bestla
                return 150000000000; // 150b
            case 33397: // Chremoas
            case 42245: // Rabisu
            case 45649: // Komodo
                return 200000000000; // 200b
            case 45531: // Victor
                return 230000000000;
            case 48635: // Tiamat
            case 60764: // Laelaps
                return 230000000000;
            case 47512: // 'Moreau' Fortizar
            case 45647: // Caiman
                return 60000000000; // 60b
            case 9860: // Polaris
            case 11019: // Cockroach
                return 1000000000000; // 1 trillion, rare dev ships
            case 42126: // Vanquisher
                return 650000000000;
            case 42241: // Molok
                if ($date <= "2019-07-01") {
                    return 350000000000; // 350b
                }
                return 650000000000;
            // Rare cruisers
            case 11940: // Gold Magnate
            if ($date <= "2020-01-25") {
                return 500000000; // 500b
            }
		    return 3400000000000;	// 3.2t
            case 635: // Opux Luxury Yacht
            case 11011: // Guardian-Vexor
            case 25560: // Opux Dragoon Yacht
            case 33395: // Moracha
                return 500000000000; // 500b
                // Rare battleships
            case 13202: // Megathron Federate Issue
            case 11936: // Apocalypse Imperial Issue
            case 11938: // Armageddon Imperial Issue
            case 26842: // Tempest Tribal Issue
                return 750000000000; // 750b
            case 26840: // Raven State Issue
                return 2500000000000;
            case 47514: // 'Horizon' Fortizar
                return 60000000000; // Too much market bugginess, hardcoding price
            default:
                return 0;
        }
    }
}
