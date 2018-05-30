<?php

namespace Forikal\GsheetXml\Test;

use Forikal\GsheetXml\Application\Service\GoogleDriveProcessService;
use Forikal\GsheetXml\Application\Service\XmlSerializer;
use Forikal\GsheetXml\Model\InventoryFactory;
use PHPUnit\Framework\TestCase;

class GoogleDriveProcessServiceTest extends TestCase
{
    public function testUrlRecognition()
    {
        $service = new GoogleDriveProcessService('foo', new InventoryFactory(), new XmlSerializer());

        $spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit';
        $folderUrl = 'https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';

        $this->assertTrue($service->isFolder($folderUrl));
        $this->assertFalse($service->isFolder($spreadsheetUrl));

        $this->assertTrue($service->isSpreadsheet($spreadsheetUrl));
        $this->assertFalse($service->isSpreadsheet($folderUrl));
    }

    public function testFolderUrlParsing()
    {
        $service = new GoogleDriveProcessService('foo', new InventoryFactory(), new XmlSerializer());
        $folderUrl = 'https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';
        $folderId = 'xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';
        $this->assertEquals($folderId, $service->parseFolderIdFromUrl($folderUrl));
    }

    public function testSpreadsheetUrlParsing()
    {
        $service = new GoogleDriveProcessService('foo', new InventoryFactory(), new XmlSerializer());
        $spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit';
        $spreadsheetId = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
        $this->assertEquals($spreadsheetId, $service->parseSpreadsheetIdFromUrl($spreadsheetUrl));
    }
}
