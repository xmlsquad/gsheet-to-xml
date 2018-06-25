#!/usr/bin/env php
<?php
require __DIR__ . '/../vendor/autoload.php';

use XmlSquad\GsheetXml\Command\GsheetToXmlCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$command = new GsheetToXmlCommand();
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();
