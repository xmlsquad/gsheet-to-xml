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

        throw new Exception('URL is not either Google Spreadsheet nor Google Drive Folder');
    }

    public function parseFolderIdFromUrl(string $url): ?string
    {
        preg_match("/\/folders\/([a-zA-Z0-9-_]+)\/?/", $url, $result);

        return $result[1] ?? null;
    }

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

    public function isSpreadsheet(string $url): bool
    {
        if (strpos($url, '/spreadsheets/') !== false) {
            return true;
        }

        return false;
    }

    private function makeClient()
    {
        $clientFactory = new GoogleClientFactory();
        $client = $clientFactory->createClient($this->credentialsPath);

        return $client;
    }

    public function processSpreadsheet(string $url): string
    {
        $spreadsheetId = $this->parseSpreadsheetIdFromUrl($url);
        if (true === empty($spreadsheetId)) {
            throw new Exception('Cant parse spreadsheet ID from the URL ' . $url);
        }

        $client = $this->makeClient();
        $service = new GoogleSpreadsheetReadService($client);
        $data = $service->getSpreadsheetData($spreadsheetId);

        $inventories = [];
        foreach ($data as $inventoryData) {
            $inventories[] = $this->inventoryFactory->make($inventoryData, $url);
        }

        $xml = $this->xmlSerializer->serializeSingleProduct($inventories);

        return $xml;
    }

    public function processFolder(string $url)
    {
        $folderId = $this->parseFolderIdFromUrl($url);
        if (true === empty($folderId)) {
            throw new Exception('Cant parse folder ID from the URL ' . $url);
        }

        $client = $this->makeClient();
        $driveService = new GoogleDriveFolderReadService($client);
        $spreadsheetFileIds = $driveService->listSpreaadsheetsInFolder($folderId);

        $spreadsheetService = new GoogleSpreadsheetReadService($client);
        $spreadsheetsInventories = [];
        foreach ($spreadsheetFileIds as $spreadsheetFileId) {
            $data = $spreadsheetService->getSpreadsheetData($spreadsheetFileId);
            $inventories = [];
            foreach ($data as $inventoryData) {
                $inventories[] = $this->inventoryFactory->make($inventoryData, $url);
            }

            $spreadsheetsInventories[] = [
                'spreadsheetId'          => $spreadsheetFileId,
                'spreadsheetInventories' => $inventories,
            ];
        }

        $xml = $this->xmlSerializer->serializeMultipleProducts($spreadsheetsInventories);

        return $xml;
    }
}
