<?php

namespace Forikal\GsheetXml\Application\Service;

use Exception;
use Forikal\GsheetXml\Model\InventoryFactory;

class GoogleDriveProcessService
{
    /** @var string string */
    private $credentialsPath;

    /** @var InventoryFactory */
    private $inventoryFactory;

    /** @var XmlSerializer */
    private $xmlSerializer;

    public function __construct(
        string $credentialsPath,
        InventoryFactory $inventoryFactory,
        XmlSerializer $xmlSerializer
    ) {
        $this->credentialsPath = $credentialsPath;
        $this->inventoryFactory = $inventoryFactory;
        $this->xmlSerializer = $xmlSerializer;
    }

    public function process($url)
    {
        if ($this->isSpreadsheet($url)) {
            return $this->processSpreadsheet($url);
        }

        if ($this->isFolder($url)) {
            return $this->processFolder($url);
        }

        throw new Exception('URL is not either Spreadsheet nor folder');
    }

    // @todo test
    public function isFolder(string $url): bool
    {
        if (strpos($url, '/folders/') !== false) {
            return true;
        }

        return false;
    }

    /**
     * @see https://developers.google.com/sheets/api/guides/concepts
     */
    public function parseSpreadsheetIdFromUrl(string $url): ?string
    {
        preg_match("/\/spreadsheets\/d\/([a-zA-Z0-9-_]+)/", $url, $result);

        return $result[1] ?? null;
    }

    // @todo test
    public function isSpreadsheet(string $url): bool
    {
        if (strpos($url, '/spreadsheets/') !== false) {
            return true;
        }

        return false;
    }

    public function processSpreadsheet(string $url): string
    {
        $spreadsheetId = $this->parseSpreadsheetIdFromUrl($url);
        if (true === empty($spreadsheetId)) {
            throw new Exception('Cant parse spreadsheet ID from the URL ' . $url);
        }

        $clientFactory = new GoogleClientFactory();
        $client = $clientFactory->createClient($this->credentialsPath);

        $service = new GoogleSpreadsheetReadService($client);
        $data = $service->getSpreadsheetData($spreadsheetId);

        $inventories = [];
        foreach ($data as $inventoryData) {
            $inventories[] = $this->inventoryFactory->make($inventoryData, $url);
        }

        $xml = $this->xmlSerializer->serialize($inventories);

        return $xml;
    }

    public function processFolder(string $folderId)
    {

    }
}
