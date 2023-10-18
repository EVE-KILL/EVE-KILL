<?php

namespace EK\Commands\Killmails;

use Composer\Autoload\ClassLoader;
use EK\Console\Api\ConsoleCommand;
use EK\Models\Killmails;

class ParseKillmails extends ConsoleCommand
{
    public string $signature = 'parse:killmails { killID? : Parse a single killmail }';
    public string $description = 'Parse all killmails manually, in chunks of 1000 at a time (Or a single one)';

    public function __construct(
        protected ClassLoader $autoloader,
        protected killmails $killmails,
    ) {
        parent::__construct();
    }


    final public function handle(): void
    {
        $killID = $this->killID;

        if ($killID === null) {
            // Loop over all the killmails, 1000 at a time, looking for the fetched = false flag, then call the parser pr. killmail
        } else {
            // Call the parser for a single killmail
        }

        dd($killID);

    }
}
