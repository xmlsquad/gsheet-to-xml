<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 26/06/2018
 * Time: 17:30
 */

namespace XmlSquad\GsheetXml\Command;

use Exception;
use Symfony\Component\Console\Command\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use Google_Service_Drive;
use Google_Service_Sheets;

use XmlSquad\Library\GoogleAPI\GoogleAPIClient;
use XmlSquad\GsheetXml\Application\Service\XmlSerializer;
use XmlSquad\GsheetXml\Model\DomainGSheetObjectFactoryInterface;




/**
 * Base class for all GSheetToXml commands.
 *
 *
 * Contains common logic related to the mechanics of:
 *  defining and getting common options,
 *  accessing Google Api, reporting access errors,
 *  collecting a Google Sheet or Drive Folder of Sheets,
 *  invoking the processing of the Google Url into Xml and returning a return code.
 * 
 * Concrete classes that extend this can be responsible for:
 *  creating the classes that hold the logic relating to the particular domain model which is
 *  represented by the sheets being collected/ xml being written.
 *  adding any intermediate control steps that might be required by the particular use-case.
 * 
 *
 * @author Zoran Antolović
 * @author Johnnie Walker
 */
abstract class AbstractGSheetToXmlCommand extends Command
{

    /**
     * Executes the current command.
     *
     * @return null|int null or 0 if everything went fine, or an error code
     */
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

        //Delegate to the concrete class to perform the processing.
        $this->processDataSource(
            $output,
            $this->createGoogleDriveProcessService(
                $googleClient,
                $this->doCreateDomainGSheetObjectFactory(),
                $this->doCreateXmlSerializer()),
            $this->getDataSourceOptions($input)
        );

        //If all went well.
        return 0;
    }


    /**
     * Configure the options that are common to most GSheetToXml commands.
     *
     * Returns $this so it can be chained with other configure methods.
     *
     * @return $this
     */
    protected function configureGenericOptions()
    {
        $this->doConfigureDataSourceOptions();
        $this->doConfigureGApiConnectionOptions();

        return $this;
    }

    protected function doConfigureGApiConnectionOptions()
    {
        $this
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


    protected function doConfigureDataSourceOptions()
    {
        $this
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
            );
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
    protected function createGoogleDriveProcessService(
        GoogleAPIClient $client,
        DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory,
        XmlSerializer $xmlSerializer){

        //Delegate to the concrete class for implementation.
        return $this->doCreateGoogleDriveProcessService(
            $client,
            $domainGSheetObjectFactory,
            $xmlSerializer);
    }

    /**
     *
     *
     * @param GoogleAPIClient $client
     * @param DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory
     * @param XmlSerializer $xmlSerializer
     * @return mixed
     */
    abstract protected function doCreateGoogleDriveProcessService(
        GoogleAPIClient $client,
        DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory,
        XmlSerializer $xmlSerializer);

    /**
     * Get GApiConnectionOption - [client-secret-file]
     *
     * @param InputInterface $input
     * @return mixed
     */
    protected function getCredentialsPathOption(InputInterface $input) {
        return $input->getOption('client-secret-file');
    }

    /**
     * Get GApiConnectionOption [access-token-file]
     *
     * @param InputInterface $input
     * @return mixed
     */
    protected function getAccessTokenFileOption(InputInterface $input){
        return $input->getOption('access-token-file');
    }

    /**
     * Get GApiConnectionOption [force-authenticate]
     *
     * @param InputInterface $input
     * @return mixed
     */
    protected function getForceAuthenticateOption(InputInterface $input){
        return $input->getOption('force-authenticate');
    }

    /**
     * Get DataSourceOption [recursive]
     *
     * @param InputInterface $input
     * @return mixed
     */
    protected function getIsRecursiveOption(InputInterface $input){
        return $input->getOption('recursive');
    }

    /**
     * Get DataSourceOption [drive-url]
     *
     * @param InputInterface $input
     * @return mixed
     */
    protected function getDriveUrlOption(InputInterface $input){
        return $input->getArgument('drive-url');
    }

    /**
     * Finds the full path to the credentials file.
     *
     * @param $credentialsPathOption
     * @return string|null string if found, null if not found.
     */
    protected function findFullCredentialsPath($credentialsPathOption){

        if (!$this->isFullCredentialsPathFindable($this->fileOptionToFullPath($credentialsPathOption))){
            return NULL;
        }
        return $this->fileOptionToFullPath($credentialsPathOption);
    }

    /**
     * Determine if path is findable.
     *
     * @param $fullCredentialsPath
     * @return bool TRUE if can be found. Otherwise FALSE.
     */
    protected function isFullCredentialsPathFindable($fullCredentialsPath){
        if (false === is_file($fullCredentialsPath)){
            return FALSE;
        }
        return TRUE;
    }

    /**
     * Convert path passed as option to full path.
     *
     * We currently expect a 'path relative to working directory'
     * where the command was invoked from.
     *
     * @param $relativePath
     * @return string
     */
    protected function fileOptionToFullPath($relativePath){
        return getcwd() . '/' . ltrim($relativePath, '/');
    }




}
