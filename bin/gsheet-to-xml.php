#!/usr/bin/env php
<?php


//START - Autoloader inclusion

//Determine if autoloader can be found.
$possibleAutoloaderPaths = array(
    __DIR__.'/../../../../vendor/autoload.php', //for when package bin is in vendor directory
    __DIR__.'/../vendor/autoload.php' //for when standalone bin is in root
    );

$autoloaderFound = FALSE;
foreach ($possibleAutoloaderPaths as $autoloaderFile) {
    if (file_exists($autoloaderFile)) {
        $autoloaderFound = TRUE;
        break;
    }
}

//Either require autoloader.php or gracefully report error.
if ($autoloaderFound){
    require_once $autoloaderFile;
} else {
    //tell the user in a friendly way:
    fwrite(STDERR, 'autoload.php not found.'. PHP_EOL);
    fwrite(STDERR, 'Searched in:'. PHP_EOL);

    foreach ($possibleAutoloaderPaths as $autoloaderFile) {
        fwrite( STDERR, '[' . $autoloaderFile . ']'. PHP_EOL);
        }

    fwrite(STDERR, PHP_EOL .'Maybe you forgot to run \'composer install\'?'. PHP_EOL . PHP_EOL);

    //Exit with non-zero (i.e error) code.
    exit(1);
}
//END - Autoloader inclusion

use XmlSquad\GsheetXml\Command\GsheetToXmlCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$command = new GsheetToXmlCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
