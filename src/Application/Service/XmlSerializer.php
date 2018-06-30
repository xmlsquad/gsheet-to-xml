<?php

namespace XmlSquad\GsheetXml\Application\Service;

use DOMDocument;
use XmlSquad\GsheetXml\Model\Domain\Inventory;
use XmlSquad\GsheetXml\Model\Domain\StockItem;

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
        $inventoryXmlElement->setAttribute('src-sheet', $inventory->getSpreadsheetName());
        $inventoryXmlElement->setAttribute('src-tab', $inventory->getSheetName());
        $inventoryXmlElement->setAttribute('src-sheet-url', $inventory->getSpreadsheetUrl());

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

            $purposesXmlElement = $dom->createElement('Purposes');
            $purposesXmlElement->appendChild($dom->createElement('Purpose', $stockItem->getPurpose()));
            $stockItemXmlElement->appendChild($purposesXmlElement);

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

    public function serializeDomainGSheetObjects(array $inventories)
    {
        $dom = new DomDocument("1.0", "UTF-8");
        $products = $dom->createElement('Products');

        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $product = $dom->createElement('Product');
            $inventoryXmlElement = $this->buildInventoryElement($dom, $inventory);
            $product->appendChild($inventoryXmlElement);
            $products->appendChild($product);
        }

        $dom->appendChild($products);

        return $this->outputFormattedXml($dom);
    }
}
