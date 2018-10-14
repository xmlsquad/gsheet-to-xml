<?php

namespace XmlSquad\GsheetXml\Command;


use XmlSquad\Library\Command\AbstractGSheetProcessingCommand;
use XmlSquad\Library\Application\Service\GoogleDriveProcessService;
use XmlSquad\GsheetXml\Model\Service\InventoryXmlSerializer;
use XmlSquad\GsheetXml\Model\Domain\InventoryFactory;
use XmlSquad\Library\Model\Domain\DomainGSheetObjectFactoryInterface;
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
class GsheetToXmlCommand extends AbstractGSheetProcessingCommand
{
    protected function configure()
    {
        $this
            ->setName('inventory:gsheet-to-xml')
            ->setDescription('Convert Inventory GSheet file to XML')
            ->setHelp('Fetch and convert Google Drive Folder / Sheets to XML.')
            ->configureGSheetProcessingConsoleParameters();
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
    protected function processDataSource(OutputInterface $output, $service, $dataSourceOptions)
    {
        $this->typeCheckGoogleDriveProcessService($service);
        $output->writeln($service->processGoogleUrl($this->doCreateGoogleDriveProcessor(),$dataSourceOptions['url'], $dataSourceOptions['recursive'], $this->doCreateDomainGSheetObjectFactory()));
    }

    protected function typeCheckGoogleDriveProcessService(GoogleDriveProcessService $service){
        //purely as a typecheck because the abstract processDataSource method's interface is left loose.
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
            'url' => $this->getDriveUrlArgument($input),
            'recursive' => $this->getIsRecursiveOption($input));
    }

    /**
     * Creates the class that converts the model to XML.
     *
     *
     * @return InventoryXmlSerializer
     */
    protected function doCreateGoogleDriveProcessor(){
        return new InventoryXmlSerializer();
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

}