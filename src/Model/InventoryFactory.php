<?php

namespace Forikal\GsheetXml\Model;

class InventoryFactory
{
    public function make(array $data, string $url): Inventory
    {
        $inventory = new Inventory();
        $inventory->setSpreadsheetUrl($url);
        $inventory->setSheetName($data['sheetTitle'] ?? null);
        $inventory->setSpreadsheetName($data['spreadsheetTitle'] ?? null);

        $stockItems = $this->processStockItems($data);
        $inventory->setStockItems($stockItems);

        return $inventory;
    }

    private function processStockItems(array $data)
    {
        $stockItems = [];

        if (false === isset($data['values']) || true === empty($data['values'])) {
            return $stockItems;
        }

        foreach ($data['values'] as $rowData) {
            $stockItem = new StockItem();

            foreach ($rowData as $heading => $value) {
                $heading = trim($heading);
                if ($heading === 'Name') {
                    $stockItem->setName($value);
                }

                if ($heading === 'KNumber') {
                    $stockItem->setKNumber($value);
                }

                if ($heading === 'KNumberExists') {
                    $stockItem->setKNumberExists($value);
                }

                if ($heading === 'Quantity') {
                    $stockItem->setInventoryQuantity($value);
                }

                if ($heading === 'AlternativeNumber') {
                    $stockItem->setAlternativeNumber($value);
                }

                if ($heading === 'Purpose') {
                    $stockItem->setPurpose($value);
                }

                if ($heading === 'PurposeOther') {
                    $stockItem->setPurposeOther($value);
                }
            }

            $stockItems[] = $stockItem;
        }

        return $stockItems;
    }

}