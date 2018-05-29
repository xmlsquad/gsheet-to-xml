<?php

namespace Forikal\GsheetXml\Application\Service;

use Google_Client;
use Google_Service_Sheets;

class GoogleSpreadsheetReadService
{
    /** @var Google_Client */
    private $client;

    public function __construct(Google_Client $client)
    {
        $this->client = $client;
    }

    public function getSpreadsheetData($spreadsheetId)
    {
        $service = new Google_Service_Sheets($this->client);
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);
        $sheets = $spreadsheet->getSheets();

        $data = [];

        /** @var \Google_Service_Sheets_Sheet $sheet */
        foreach ($sheets as $sheet) {
            $title = $sheet->getProperties()->getTitle();

            /**
             * If the sheet name has spaces or starts with a bracket, surround the sheet name with single quotes ('), e.g 'Sheet One'!A1:B2. For simplicity, it is safe to always surround the sheet name with single quotes.
             * @see https://developers.google.com/sheets/api/guides/concepts
             */
            $range = "'$title'!A1:H";
            $data = $service->spreadsheets_values->get(
                $spreadsheetId,
                $range,
                ['majorDimension' => 'ROWS']
            );

            $values = isset($data['values']) ? $data['values'] : null;

            $sheetData = [
                'title'  => $sheet->getProperties()->getTitle(),
                'values' => $values,
            ];

            $data[] = $sheetData;
        }

        return $data;
    }
}