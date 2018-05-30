<?php

namespace Forikal\GsheetXml\Application\Service;

use Google_Client;
use Google_Service_Sheets;

class GoogleClientFactory
{
    public function createClient($credentialsPath)
    {
        $client = new Google_Client();
        $client->setAuthConfig($credentialsPath);
        $client->setApplicationName('theName');
        $client->setScopes([
            Google_Service_Sheets::SPREADSHEETS_READONLY,
            Google_Service_Sheets::DRIVE_READONLY,
        ]);

        return $client;
    }
}
