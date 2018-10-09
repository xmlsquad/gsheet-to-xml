<?php

namespace XmlSquad\GsheetXml\Application\Service;

use Exception;
use XmlSquad\GsheetXml\Model\Domain\DomainGSheetObjectFactoryInterface;
use XmlSquad\Library\GoogleAPI\GoogleAPIClient;


use XmlSquad\GsheetXml\Model\Service\XmlSerializerInterface;

class GoogleDriveProcessService
{
    /** @var GoogleAPIClient */
    private $client;

    /** @var DomainGSheetObjectFactoryInterface */
    private $domainGSheetObjectFactory;

    /** @var XmlSerializer */
    private $xmlSerializer;

    public function __construct(
        GoogleAPIClient $client,
        DomainGSheetObjectFactoryInterface $domainGSheetObjectFactory,
        XmlSerializerInterface $xmlSerializer
    ) {
        $this->client = $client;
        $this->domainGSheetObjectFactory = $domainGSheetObjectFactory;
        $this->xmlSerializer = $xmlSerializer;
    }

    public function googleUrlToXml(string $url, bool $recursive, $headingValues)
    {
        if ($this->isSpreadsheet($url)) {
            return $this->googleSpreadsheetToXml($url, $headingValues);
        }

        if ($this->isFolder($url)) {
            return $this->googleFolderToXml($url, $recursive, $headingValues);
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

    protected function googleSpreadsheetToXml(string $spreadsheetUrl, $headingValues): string
    {
        $spreadsheetId = $this->parseSpreadsheetIdFromUrl($spreadsheetUrl);
        if (true === empty($spreadsheetId)) {
            throw new Exception('Cant parse spreadsheet ID from the URL [' . $spreadsheetUrl .']');
        }

        $service = new GoogleSpreadsheetReadService($this->client);
        $data = $service->getSpreadsheetData($spreadsheetId, $headingValues);

        $domainGSheetObjects = [];
        foreach ($data as $domainGSheetObjectData) {
            $domainGSheetObjects[] = $this->domainGSheetObjectFactory->createDomainGSheetObject($domainGSheetObjectData, $spreadsheetUrl);
        }

        $xml = $this->xmlSerializer->serializeDomainGSheetObjects($domainGSheetObjects);

        return $xml;
    }

    protected function googleFolderToXml(string $url, bool $recursive, $headingValues)
    {
        $folderId = $this->parseFolderIdFromUrl($url);
        if (true === empty($folderId)) {
            throw new Exception('Cant parse folder ID from the URL ' . $url);
        }

        $driveService = new GoogleDriveFolderReadService($this->client);
        $spreadsheetFileIds = $driveService->listSpreaadsheetsInFolder($folderId, $recursive);

        /**
         * Each Google Sheet tab represents one of these: <Product><Inventory>...data here..</Inventory></Product>.
         */
        $spreadsheetService = new GoogleSpreadsheetReadService($this->client);
        $domainGSheetObjects = [];
        foreach ($spreadsheetFileIds as $spreadsheetFileId) {
            $sheetsData = $spreadsheetService->getSpreadsheetData($spreadsheetFileId, $headingValues);
            $sheetUrl = "https://docs.google.com/spreadsheets/d/{$spreadsheetFileId}/";

            foreach ($sheetsData as $sheetData) {
                $domainGSheetObjects[] = $this->domainGSheetObjectFactory->createDomainGSheetObject($sheetData, $sheetUrl);
            }
        }

        $xml = $this->xmlSerializer->serializeDomainGSheetObjects($domainGSheetObjects);

        return $xml;
    }
}
