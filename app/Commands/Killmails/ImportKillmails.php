<?php

namespace EK\Commands\Killmails;

use Composer\Autoload\ClassLoader;
use EK\Console\Api\ConsoleCommand;
use EK\Models\Killmails;

class ImportKillmails extends ConsoleCommand
{
    public string $signature = 'import:killmails';
    public string $description = 'Import killmail data from JSON blobs';

    public function __construct(
        protected ClassLoader $autoloader,
        protected killmails $killmails,
    ) {
        parent::__construct();
    }


    final public function handle(): void
    {
        ini_set('memory_limit', '-1');

        $backups = glob(\BASE_DIR . '/resources/killmails-*.json.gz');
        foreach($backups as $backup) {
            $this->output->writeln('Importing ' . $backup);
            $json = gzdecode(file_get_contents($backup));
            $killmails = json_decode($json, true);
            $bigInsert = [];
            foreach($killmails as $killmail) {
                $killmail['killID'] = $killmail['killmail_id'];
                unset($killmail['killmail_id']);
                $bigInsert[] = $killmail;
            }

            $this->killmails->collection->insertMany($bigInsert);
        }
    }
}
