<?php

namespace EK\EVE\Seeds;

use EK\EVE\Api\SeedInterface;
use League\Container\Container;
use Symfony\Component\Console\Helper\ProgressBar;

class InvFlags extends SeedInterface
{
    public string $collectionName = 'invflags';
    public string $fileName = 'invFlags.yaml';

    public function __construct(
        protected Container $container,
        protected \EK\EVE\Models\InvFlags $invflags
    ) {

    }

    public function execute(ProgressBar $progressBar): void
    {
        $sqlite = $this->getSqliteConnection();
        $stmt = $sqlite->query('SELECT * FROM invFlags');
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        foreach($result as $flag) {
            $this->invflags->setData([
                'flagID' => (int) $flag['flagID'],
                'flagName' => $flag['flagName'],
                'flagText' => $flag['flagText'],
                'orderID' => (int) $flag['orderID'],
            ]);
            $this->invflags->save();
        }
    }

    public function getItemCount(): int
    {
        $sqlite = $this->getSqliteConnection();
        $stmt = $sqlite->query('SELECT * FROM invFlags');
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return count($result);
    }
}
