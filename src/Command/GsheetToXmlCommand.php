<?php

namespace XmlSquad\GsheetXml\Command;


use XmlSquad\GsheetXml\Command\AbstractGSheetToXmlCommand;
use XmlSquad\GsheetXml\Application\Service\GoogleDriveProcessService;
use XmlSquad\GsheetXml\Application\Service\XmlSerializer;
use XmlSquad\GsheetXml\Model\InventoryFactory;


use XmlSquad\GsheetXml\Model\DomainGSheetObjectFactoryInterface;


use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use XmlSquad\Library\GoogleAPI\GoogleAPIClient;

/**
 * Grabs Inventory GSheet/s and converts it/them To Xml
 *
 *
 * The base class contains the infrastructural mechanics
 * leaving this concrete class responsible for:
 *  adding extra options
 *  creating the classes that hold the logic relating to the particular domain model which is
 *  represented by the sheets being collected/ xml being written.
 *  and, if required, adding any intermediate control steps that might be required
 *  by the particular use-case.
 *
 *
 * @author Zoran Antolović
 * @author Johnnie Walker
 */
class GsheetToXmlCommand extends AbstractGSheetToXmlCommand
{
    protected function configure()
    {
        $this
            ->setName('inventory:gsheet-to-xml')
            ->setDescription('Convert Inventory GSheet file to XML')
            ->setHelp('Fetch and convert Google Drive Folder / Sheets to XML.')
            ->configureGenericOptions();
    }

    /**
     * Invoke the GoogleDriveProcessService to process the data source.
     *
     *
     * @param OutputInterface $output
     * @param GoogleDriveProcessService $service
     * @param $dataSourceOptions
     * @throws \Exception
     */
    protected function processDataSource(OutputInterface $output, GoogleDriveProcessService $service, $dataSourceOptions)
    {

        $output->writeln($service->googleUrlToXml($dataSourceOptions['url'], $dataSourceOptions['recursive']));
    }


    /**
     * Returns 'url' and 'recursive' and, maybe, custom Datasource options.
     *
     * This is where we customise the options that
     * are injected into the doProcessDataSource method.
     *
     *@return array of DataSourceOptions
     */
    protected function getDataSourceOptions(InputInterface $input){
        return array(
            'url' => $this->getDriveUrlOption($input),
            'recursive' => $this->getIsRecursiveOption($input));
    }

    /**
     * Creates the class that converts the model to XML.
     *
     *
     * @return XmlSerializer
     */
    protected function doCreateXmlSerializer(){
        return new XmlSerializer();
    }

    /**
     * Creates the factory which creates the domain object that represents the contents of the kind of Google Sheet that this command processes.
     *
     *
     * @return DomainGSheetObjectFactoryInterface
     */
    protected function doCreateDomainGSheetObjectFactory():InventoryFactory {
        return new InventoryFactory();
    }

    /**
     * Factory method for GoogleDriveProcessService object.
     *
     *
     * @param GoogleAPIClient $client
     * @param DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory
     * @param XmlSerializer $xmlSerializer
     * @return GoogleDriveProcessService
     */
    protected function doCreateGoogleDriveProcessService(
        GoogleAPIClient $client,
        DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory,
        XmlSerializer $xmlSerializer){

        return new GoogleDriveProcessService(
            $client,
            $domainGSheetObjectFactory,
            $xmlSerializer);
    }
}