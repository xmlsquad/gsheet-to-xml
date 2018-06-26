<?php

namespace XmlSquad\GsheetXml\Test;

use XmlSquad\GsheetXml\Application\Service\GoogleDriveProcessService;
use XmlSquad\GsheetXml\Application\Service\XmlSerializer;
use XmlSquad\GsheetXml\Model\InventoryFactory;
use XmlSquad\Library\GoogleAPI\GoogleAPIClient;
use PHPUnit\Framework\TestCase;

class GoogleDriveProcessServiceTest extends TestCase
{
    public function testUrlRecognition()
    {
        $googleClient = new GoogleAPIClient();
        $service = new GoogleDriveProcessService($googleClient, new InventoryFactory(), new XmlSerializer());

        $spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit';
        $folderUrl = 'https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';

        $this->assertTrue($service->isFolder($folderUrl));
        $this->assertFalse($service->isFolder($spreadsheetUrl));

        $this->assertTrue($service->isSpreadsheet($spreadsheetUrl));
        $this->assertFalse($service->isSpreadsheet($folderUrl));
    }

    public function testFolderUrlParsing()
    {
        $googleClient = new GoogleAPIClient();
        $service = new GoogleDriveProcessService($googleClient, new InventoryFactory(), new XmlSerializer());
        $folderUrl = 'https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';
        $folderId = 'xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';
        $this->assertEquals($folderId, $service->parseFolderIdFromUrl($folderUrl));
    }

    public function testSpreadsheetUrlParsing()
    {
        $googleClient = new GoogleAPIClient();
        $service = new GoogleDriveProcessService($googleClient, new InventoryFactory(), new XmlSerializer());
        $spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit';
        $spreadsheetId = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $this->assertEquals($spreadsheetId, $service->parseSpreadsheetIdFromUrl($spreadsheetUrl));
    }
}
