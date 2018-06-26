<?php

namespace XmlSquad\GsheetXml\Command;

use Exception;
use XmlSquad\GsheetXml\Command\AbstractGSheetToXmlCommand;
use XmlSquad\GsheetXml\Application\Service\GoogleDriveProcessService;
use XmlSquad\GsheetXml\Application\Service\XmlSerializer;
use XmlSquad\GsheetXml\Model\InventoryFactory;
use XmlSquad\Library\GoogleAPI\GoogleAPIClient;
use Google_Service_Drive;
use Google_Service_Sheets;

use Symfony\Component\Console\Input\InputInterface;

use Symfony\Component\Console\Output\OutputInterface;

class GsheetToXmlCommand extends AbstractGSheetToXmlCommand
{
    protected function configure()
    {
        $this
            ->setName('inventory:gsheet-to-xml')
            ->setDescription('Convert GSheet file to XML')
            ->setHelp('Fetch and convert Google Drive Folder / Sheets to XML.');

        $this->doConfigureGenericOptions();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fullCredentialsPath = $this->findFullCredentialsPath($this->getCredentialsPathOption($input));
        if (!$fullCredentialsPath) {
            throw new Exception('Credentials file not found.');
        }


        $googleClient = new GoogleAPIClient();
        $googleClient->authenticateFromCommand(
            $input,
            $output,
            $fullCredentialsPath,
            $this->fileOptionToFullPath($this->getAccessTokenFileOption($input)),
            [Google_Service_Drive::DRIVE_READONLY, Google_Service_Sheets::SPREADSHEETS_READONLY],
            $this->getForceAuthenticateOption($input)
        );

        $service = $this->createGoogleDriveProcessService(
            $googleClient,
            $this->doCreateDomainGSheetObjectFactory(),
            $this->doCreateXmlSerializer());

        $output->writeln($service->googleUrlToXml($this->getDriveUrlOption($input), $this->getIsRecursiveOption($input)));
    }

    /**
     * Factory method for GoogleDriveProcessService object.
     *
     * Could be overridden by concrete class if special processing needed.
     *
     * @param GoogleAPIClient $client
     * @param InventoryFactory $domainGSheetObjectFactory
     * @param XmlSerializer $xmlSerializer
     * @return GoogleDriveProcessService
     */
    protected function createGoogleDriveProcessService(
        GoogleAPIClient $client,
        InventoryFactory $domainGSheetObjectFactory,
        XmlSerializer $xmlSerializer){
        
        return new GoogleDriveProcessService(
            $client,
            $domainGSheetObjectFactory,
            $xmlSerializer);
    }

    protected function doCreateXmlSerializer(){
        return new XmlSerializer();
    }

    /**
     * Creates the factory which creates the domain object that represents the contents of the kind of Google Sheet that this command processes.
     * @return InventoryFactory
     */
    protected function doCreateDomainGSheetObjectFactory(){
        return new InventoryFactory();
    }




    protected function getDriveUrlOption(InputInterface $input){
        return $input->getArgument('drive-url');
    }

    protected function getIsRecursiveOption(InputInterface $input){
        return $input->getOption('recursive');
    }

    protected function getForceAuthenticateOption(InputInterface $input){
        return $input->getOption('force-authenticate');
    }

    protected function getCredentialsPathOption(InputInterface $input) {
        return $input->getOption('client-secret-file');
    }

    protected function getAccessTokenFileOption(InputInterface $input){
        return $input->getOption('access-token-file');
    }

    protected function fileOptionToFullPath($relativePath){
        return getcwd() . '/' . ltrim($relativePath, '/');
    }


    protected function isFullCredentialsPathFindable($fullCredentialsPath){
        if (false === is_file($fullCredentialsPath)){
            return FALSE;
        }
        return TRUE;
    }

    protected function findFullCredentialsPath($credentialsPathOption){

        if (!$this->isFullCredentialsPathFindable($this->fileOptionToFullPath($credentialsPathOption))){
            return NULL;
        }
        return $this->fileOptionToFullPath($credentialsPathOption);
    }



}