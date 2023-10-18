<?php

namespace EK\Commands;

use EK\Console\Api\ConsoleCommand;
use EK\Models\Config;
use League\Container\Container;

class Converter extends ConsoleCommand
{
    protected string $signature = 'converter';
    protected string $description = 'Convert the latest SDE into usable data, and import to mongodb';

    public function __construct(
        protected Container $container,
        protected Config $config
    ) {
        parent::__construct();
    }

    final public function handle(): void
    {
        $workingDir = '/tmp';
        $sdeUrl = 'https://eve-static-data-export.s3-eu-west-1.amazonaws.com/tranquility/sde.zip';
        $sdeMd5 = 'https://eve-static-data-export.s3-eu-west-1.amazonaws.com/tranquility/checksum';

        // Get the md5
        $md5 = file_get_contents($sdeMd5);

        // Get the currently set SDE md5 from the config table
        $storedMd5 = $this->config->findOne(['key' => 'sde_md5'])->get('value', null);

        // If the md5's match, we don't need to do anything
        if ($md5 === $storedMd5) {
            $this->out('SDE is up to date');
            return;
        }

        // Download the SDE
        $this->out('Downloading SDE');
        $sde = fopen($sdeUrl, 'rb');
        $sdeFile = fopen($workingDir . '/sde.zip', 'wb');
        stream_copy_to_stream($sde, $sdeFile);
        fclose($sde);

        // Unpack the SDE
        $this->out('Unpacking SDE');
        $zip = new \ZipArchive();
        $zip->open($workingDir . '/sde.zip');
        $zip->extractTo($workingDir);
        $zip->close();

        // Delete the zip
        unlink($workingDir . '/sde.zip');

        // Process the SDE
        $process = [
            'agents',
            'agentsInSpace',
            'ancestries',
            'bloodlines',
            'blueprints',
            'categoryIDs',
            'certificates',
            'characterAttributes',
            'contrabandTypes',
            'controlTowerResources',
            'corporationActivities',
            'dogmaAttributeCategories',
            'dogmaAttributes',
            'dogmaEffects',
            'factions',
            'graphicIDs',
            'groupIDs',
            'iconIDs',
            'marketGroups',
            'metaGroups',
            'npcCorporationDivisions',
            'npcCorporations',
            'planetSchematics',
            'races',
            'researchAgents',
            'skinLicenses',
            'skinMaterials',
            'skins',
            'stationOperations',
            'stationServices',
            'tournamentRuleSets',
            'translationLanguages',
            'typeDogma',
            'typeIDs',
            'typeMaterials',
            // Folders
            'landmarks',
            'universe'
        ];

        dd($process);

    }
}
