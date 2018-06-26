#!/usr/bin/env php
<?php

$autoloaderFound = FALSE;

$possibleAutoloaderPaths = array(
    __DIR__.'/../../../../vendor/autoload.php', //for when package is in vendor directory
    __DIR__.'/../vendor/autoload.php' //for when bin is in root
    );

foreach ($possibleAutoloaderPaths as $autoloaderFile) {
    if (@file_exists($autoloaderFile)) {
        $autoloaderFound = TRUE;
        break;
    }
}

if ($autoloaderFound){
    //Requie the autoloader
    require_once $autoloaderFile;
} else {

    //tell the user in a friendly way:
    fwrite(STDOUT, 'autoload.php not found.'. PHP_EOL);
    fwrite(STDOUT, 'Searched in:'. PHP_EOL);

    reset($possibleAutoloaderPaths);
    foreach ($possibleAutoloaderPaths as $autoloaderFile) {
        fwrite( STDOUT, '[' . $autoloaderFile . ']'. PHP_EOL);
        }

    fwrite(STDOUT, 'Maybe you forgot to run \'composer install\'?'. PHP_EOL);

    exit(1);
}


use XmlSquad\GsheetXml\Command\GsheetToXmlCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$command = new GsheetToXmlCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
