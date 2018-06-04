<?php

namespace Forikal\GsheetXml\Application\Service;

use Google_Client;
use Google_Service_Sheets;
use Google_Service_Sheets_Sheet;

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
        $spreadsheetTitle = $spreadsheet->getProperties()->getTitle();
        $sheets = $spreadsheet->getSheets();

        $data = [];

        /** @var Google_Service_Sheets_Sheet $sheet */
        foreach ($sheets as $sheet) {
            $title = $sheet->getProperties()->getTitle();

            /**
             *  If a Google Sheet's tab is named foo_, then it is assumed to be 'private' and
             *  should be explicitly ignored, but it should be noted (in any feedback) that it was ignored.
             */
            if ('_' === substr($title, -1)) {
                // @todo feedback that this sheet/tab was ignored?
                continue;
            }

            $sheetData = [
                'sheetTitle'       => $sheet->getProperties()->getTitle(),
                'spreadsheetTitle' => $spreadsheetTitle,
                'values'           => $this->parseSheet(
                    $service,
                    $spreadsheetId,
                    $sheet
                ),
            ];

            $data[] = $sheetData;
        }

        return $data;
    }

    private function
    parseSheet(
        Google_Service_Sheets $service,
        string $spreadsheetId,
        Google_Service_Sheets_Sheet $sheet
    ): ?array {

        /**
         * If the sheet name has spaces or starts with a bracket, surround the sheet name with single quotes ('),
         * e.g 'Sheet One'!A1:B2. For simplicity, it is safe to always surround the sheet name with single quotes.
         * @see https://developers.google.com/sheets/api/guides/concepts
         */
        $title = $sheet->getProperties()->getTitle();
        $range = "'$title'!A1:H";
        $data = $service->spreadsheets_values->get(
            $spreadsheetId,
            $range,
            ['majorDimension' => 'ROWS']
        );

        $values = null;
        if (true === isset($data['values']) && false === empty($data['values'])) {
            $values = $this->combineSheetDataWithHeadings($data['values']);
        }

        return $values;
    }

    /**
     * Transform the data so that output array has $heading => $value format
     */
    private function combineSheetDataWithHeadings(array $data): array
    {
        $headings = null;
        $outputData = [];

        foreach ($data as $row) {
            if (true === $this->isEmptyRow($row)) {
                continue;
            }

            // Skip non-headings rows until we find headings
            if (true === empty($headings) && false === $this->isHeadingsRow($row)) {
                continue;
            }

            if (true === empty($headings) && true === $this->isHeadingsRow($row)) {
                $headings = $row;
                continue;
            }

            // Slice first N elements from headings if row doesn't have values for ending columns
            $outputData[] = array_combine(
                array_slice($headings, 0, count($row)),
                $row
            );
        }

        return $outputData;
    }

    private function isEmptyRow(?array $row): bool
    {
        if (true === is_null($row)) {
            return true;
        }

        if (true === empty($row)) {
            return true;
        }

        if (true === isset($row[0]) && true === empty($row[0])) {
            return true;
        }

        return false;
    }

    private function isHeadingsRow(?array $row): bool
    {
        if (true === empty($row)) {
            return false;
        }

        $firstCellValue = $row[0] ?? null;
        if (true === empty($firstCellValue)) {
            return false;
        }

        $headingValues = [
            'Name',
            'KNumberExists',
            'KNumber',
            'Quantity',
            'AlternativeNumber',
            'Purpose',
            'PurposeOther',
        ];

        if (true === in_array(trim($firstCellValue), $headingValues)) {
            return true;
        }

        return false;
    }
}