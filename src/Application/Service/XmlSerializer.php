<?php

namespace Forikal\GsheetXml\Application\Service;

use DOMDocument;
use Forikal\GsheetXml\Model\Inventory;
use Forikal\GsheetXml\Model\StockItem;

class XmlSerializer
{
    private function outputFormattedXml(DOMDocument $dom)
    {
        $dom->xmlStandalone = true;
        $dom->encoding = 'UTF-8';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        return $dom->saveXML(null, LIBXML_NOEMPTYTAG);
    }

    private function buildInventoryElement(DOMDocument $dom, Inventory $inventory)
    {
        $inventoryXmlElement = $dom->createElement('Inventory');
        $inventoryXmlElement->setAttribute('src-sheet', $inventory->getSpreadsheetUrl());
        $inventoryXmlElement->setAttribute('src-tab', $inventory->getSheetName());

        /** @var StockItem $stockItem */
        foreach ($inventory->getStockItems() as $stockItem) {
            $stockItemXmlElement = $dom->createElement('StockItem');
            $stockItemXmlElement->appendChild($dom->createElement('KNumberExists', $stockItem->getKNumberExists()));
            $stockItemXmlElement->appendChild($dom->createElement('KNumber', $stockItem->getKNumber()));
            $stockItemXmlElement->appendChild($dom->createElement('AdditionalKNumbers',
                $stockItem->getAdditionalKNumbers()));
            $stockItemXmlElement->appendChild($dom->createElement('AlternativeNumber',
                $stockItem->getAlternativeNumber()));
            $stockItemXmlElement->appendChild($dom->createElement('InventoryContainerID',
                $stockItem->getInventoryContainerId()));
            $stockItemXmlElement->appendChild($dom->createElement('Name', $stockItem->getName()));

            // @todo resolve multiple values and handling here
            $stockItemXmlElement->appendChild($dom->createElement('Purposes', $stockItem->getPurpose()));

            $stockItemXmlElement->appendChild($dom->createElement('PurposeOther', $stockItem->getPurposeOther()));
            $stockItemXmlElement->appendChild($dom->createElement('InventoryQuantity',
                $stockItem->getInventoryQuantity()));
            $stockItemXmlElement->appendChild($dom->createElement('HandlingStatus', $stockItem->getHandlingStatus()));
            $stockItemXmlElement->appendChild($dom->createElement('SupplierRegistrationNumber',
                $stockItem->getSupplierRegistrationNumber()));

            $inventoryXmlElement->appendChild($stockItemXmlElement);
        }

        return $inventoryXmlElement;
    }

    public function serializeSingleProduct(array $inventories)
    {
        $dom = new DomDocument("1.0", "UTF-8");
        $products = $dom->createElement('Products');
        $product = $dom->createElement('Product');

        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $inventoryXmlElement = $this->buildInventoryElement($dom, $inventory);
            $product->appendChild($inventoryXmlElement);
        }

        $products->appendChild($product);
        $dom->appendChild($products);

        return $this->outputFormattedXml($dom);
    }

    public function serializeMultipleProducts(array $productsInventories)
    {
        $dom = new DomDocument("1.0", "UTF-8");
        $products = $dom->createElement('Products');

        foreach ($productsInventories as $productInventories) {
            $product = $dom->createElement('Product');
            $product->setAttribute('src-spreadsheet', $productInventories['spreadsheetId']);

            /** @var Inventory $inventory */
            foreach ($productInventories['spreadsheetInventories'] as $inventory) {
                $inventoryXmlElement = $this->buildInventoryElement($dom, $inventory);
                $product->appendChild($inventoryXmlElement);
            }

            $products->appendChild($product);
        }

        $dom->appendChild($products);

        return $this->outputFormattedXml($dom);
    }
}
