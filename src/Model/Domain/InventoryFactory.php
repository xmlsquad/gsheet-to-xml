<?php

namespace XmlSquad\GsheetXml\Model\Domain;

use XmlSquad\GsheetXml\Model\Domain\DomainGSheetObjectFactoryInterface;

class InventoryFactory implements DomainGSheetObjectFactoryInterface
{
    /**
     * Implements the process of making the object that is represented by a GSheet. In this case, an Inventory.
     *
     *
     * @param array $data
     * @param string $spreadsheetUrl
     * @return Inventory
     */
    public function createDomainGSheetObject(array $data, string $spreadsheetUrl)
    {
        //Delegate to the concrete class for specific implementation.
        return $this->doCreateDomainGSheetObject($data, $spreadsheetUrl);
    }

    /**
     *
     *
     *
     * @see https://en.wikipedia.org/wiki/Template_method_pattern
     *
     * @param array $data
     * @param string $spreadsheetUrl
     * @return Inventory
     */
    protected function doCreateDomainGSheetObject(array $data, string $spreadsheetUrl): Inventory {
        $inventory = new Inventory();
        $inventory->setSpreadsheetUrl($spreadsheetUrl);
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