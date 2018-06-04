<?php

namespace Forikal\GsheetXml\Command;

use Exception;
use Forikal\GsheetXml\Application\Service\GoogleDriveProcessService;
use Forikal\GsheetXml\Application\Service\XmlSerializer;
use Forikal\GsheetXml\Model\InventoryFactory;
use Forikal\Library\GoogleAPI\GoogleAPIClient;
use Google_Service_Drive;
use Google_Service_Sheets;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            )
            ->addOption(
                'recursive',
                'r',
                InputOption::VALUE_NONE,
                'if the Google Drive entity is a Google Drive folder, this option specifies whether or not to recurse through sub-directories to find sheets.'
            )
            ->addOption(
                'client-secret-file',
                null,
                InputOption::VALUE_REQUIRED,
                'The path to an application client secret file.'
            )
            ->addOption(
                'access-token-file',
                null,
                InputOption::VALUE_REQUIRED,
                'The path to an access token file. The file may not exists. If an access token file is used, the command remembers user credentials and doesn\'t require a Google authentication next time.'
            )
            ->addOption(
                'force-authenticate',
                null,
                InputOption::VALUE_NONE,
                'If set, you will be asked to authenticate even if an access token exist.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $url = $input->getArgument('drive-url');
        $recursive = $input->getOption('recursive');

        $credentialsPath = $input->getOption('client-secret-file');
        $credentialsPath = getcwd() . '/' . ltrim($credentialsPath, '/');

        if (false === is_file($credentialsPath)) {
            throw new Exception('Credentials file not found');
        }

        $accessTokenFile = $input->getOption('access-token-file');
        $accessTokenFile = getcwd() . '/' . ltrim($accessTokenFile, '/');

        $serializer = new XmlSerializer();
        $inventoryFactory = new InventoryFactory();

        $googleClient = new GoogleAPIClient();
        $googleClient->authenticateFromCommand(
            $input,
            $output,
            $credentialsPath,
            $accessTokenFile,
            [Google_Service_Drive::DRIVE_READONLY, Google_Service_Sheets::SPREADSHEETS_READONLY],
            $input->getOption('force-authenticate')
        );

        $service = new GoogleDriveProcessService($googleClient, $inventoryFactory, $serializer);
        $xml = $service->process($url, $recursive);
        $output->writeln($xml);
    }
}