#!/usr/bin/env php
<?php

set_time_limit(0);

//START - Autoloader inclusion

//Determine if autoloader can be found.
$possibleAutoloaderPaths = array(
    __DIR__.'/../../../../vendor/autoload.php', //for when package bin is in vendor directory
    __DIR__.'/../vendor/autoload.php', //for when standalone bin is in root
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
    //Tell the user in a friendly way:

    //Compose the messages.
    $feedbackLines = array(
        'The autoload.php was not found.',
        'The script searched for it at:',
    );

    foreach ($possibleAutoloaderPaths as $autoloaderFile) {
        $feedbackLines[] = '[' . $autoloaderFile . ']';
        }

    //Extra spaces to highlight the suggestion.
    $feedbackLines[] =
        PHP_EOL
        . 'Maybe you forgot to run \'composer install\'?'
        . PHP_EOL;

    //Write them out.
    foreach($feedbackLines as $line){
        fwrite(STDERR,  $line . PHP_EOL);
    }


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
