<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 26/06/2018
 * Time: 17:30
 */

namespace XmlSquad\GsheetXml\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


/**
 * Class AbstractCommand
 */
abstract class AbstractGSheetToXmlCommand extends Command
{
    protected function doConfigureGenericOptions()
    {
        $this->doConfigureDataSourceOptions();
        $this->doConfigureApiConnectionOptions();
    }

    protected function doConfigureApiConnectionOptions()
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
}
