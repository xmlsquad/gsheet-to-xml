<?php

namespace Forikal\GsheetXml\Test;

use Forikal\GsheetXml\Application\Service\GoogleDriveProcessService;
use PHPUnit\Framework\TestCase;

class GoogleDriveProcessServiceTest extends TestCase
{
    public function testUrlParsing()
    {
        $service = new GoogleDriveProcessService('foo');

        $spreadsheetUrl = 'https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit';
        $folderUrl = 'https://drive.google.com/drive/folders/xxxxxxxxxx-xxxxxxxxx-xxxxxxxxxxxx';

        $this->assertTrue($service->isFolder($folderUrl));
        $this->assertFalse($service->isFolder($spreadsheetUrl));

        $this->assertTrue($service->isSpreadsheet($spreadsheetUrl));
        $this->assertFalse($service->isSpreadsheet($folderUrl));
    }
}
