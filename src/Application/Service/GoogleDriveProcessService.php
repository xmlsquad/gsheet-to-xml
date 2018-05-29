<?php

namespace Forikal\GsheetXml\Application\Service;

use Exception;

class GoogleDriveProcessService
{
    /** @var string string */
    private $credentialsPath;

    public function __construct(string $credentialsPath)
    {
        $this->credentialsPath = $credentialsPath;
    }

    public function process($url)
    {
        if ($this->isSpreadsheet($url)) {
            $id = $this->parseSpreadsheetIdFromUrl($url);
            if (true === empty($id)) {
                throw new Exception('Cant parse spreadsheet ID from the URL ' . $url);
            }

            return $this->processSpreadsheet($id);
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

    public function processSpreadsheet(string $spreadSheetId)
    {
        $clientFactory = new GoogleClientFactory();
        $client = $clientFactory->createClient($this->credentialsPath);

        $service = new GoogleSpreadsheetReadService($client);
        $data = $service->getSpreadsheetData($spreadSheetId);
        var_dump($data);
    }

    public function processFolder(string $folderId)
    {
    }
}
