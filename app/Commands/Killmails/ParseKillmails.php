<?php

namespace EK\Commands\Killmails;

use Composer\Autoload\ClassLoader;
use EK\Console\Api\ConsoleCommand;
use EK\Models\Killmails;

class ParseKillmails extends ConsoleCommand
{
    public string $signature = 'parse:killmails
        { --debug : Debug the killmail by emitting it into the terminal }
        { killID? : Parse a single killmail }';
    public string $description = 'Parse all killmails manually, in chunks of 1000 at a time (Or a single one)';

    public function __construct(
        protected ClassLoader $autoloader,
        protected killmails $killmails,
        protected \EK\EVE\Helpers\Killmails $killmailsHelper
    ) {
        parent::__construct();
    }


    final public function handle(): void
    {
        if ($this->killID === null) {
            $unparsedCount = $this->killmails->count(['attackers' => ['$exists' => false]]);
            $progressBar = $this->progressBar($unparsedCount);
            $progressBar->setFormat("%message%\n %current%/%max% [%bar%] %percent:3s%%\n");
            $progressBar->setMessage('Parsing killmails');
            $progressBar->start();

            do {
                $unparsedKillmails = $this->killmails->find(['attackers' => ['$exists' => false]], ['limit' => 1000]);

                foreach($unparsedKillmails as $killmail) {
                    $hash = $this->killmailsHelper->getKillMailHash($killmail['killID']);
                    $parsedKillmail = $this->killmailsHelper->parseKillmail($killmail['killID'], $hash);

                    $this->killmails->setData($parsedKillmail->toArray());
                    $this->killmails->save();

                    $progressBar->advance();
                }
            } while($unparsedKillmails->count() > 0);
        } else {
            $hash = $this->killmailsHelper->getKillMailHash($this->killID);
            $parsedKillmail = $this->killmailsHelper->parseKillmail($this->killID, $hash);

            if ($this->debug === true) {
                dd($parsedKillmail->toArray());
            }

            $this->killmails->setData($parsedKillmail->toArray());
            $this->killmails->save();
        }
    }
}
