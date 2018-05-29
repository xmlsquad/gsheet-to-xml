<?php

namespace Forikal\GsheetXml\Command;

use Forikal\GsheetXml\Application\Service\GoogleClientFactory;
use Forikal\GsheetXml\Application\Service\GoogleDriveProcessService;
use Forikal\GsheetXml\Application\Service\GoogleSpreadsheetReadService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GsheetToXmlCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('forikal:gsheet-to-xml')
            ->setDescription('Convert GSheet file to XML')
            ->setHelp('Fetch and convert Google Drive Folder / Sheets to XML.')
            ->addArgument(
                'drive-url',
                InputArgument::REQUIRED,
                'The URL of the Google Drive entity (Google Sheet or Google Drive folder). is-recursive: if the Google Drive entity is a Google Drive folder, this option specifies whether or not to recurse through sub-directories to find sheets.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('drive-url');
        $output->writeln($url);

        $credentialsPath = __DIR__ . "/../../client_secret.json";

        $service = new GoogleDriveProcessService($credentialsPath);
        $service->process($url);
    }
}