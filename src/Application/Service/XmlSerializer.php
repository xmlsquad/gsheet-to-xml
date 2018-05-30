<?php

namespace Forikal\GsheetXml\Application\Service;

use DOMDocument;
use Forikal\GsheetXml\Model\Inventory;
use Forikal\GsheetXml\Model\StockItem;
use SimpleXMLElement;

class XmlSerializer
{
    public function serialize(array $inventories)
    {
        $products = new SimpleXMLElement("<Products></Products>");
        $product = $products->addChild('Product');

        /** @var Inventory $inventory */
        foreach ($inventories as $inventory) {
            $inventoryXmlElement = $product->addChild('Inventory');
            $inventoryXmlElement->addAttribute('src-sheet', $inventory->getSpreadsheetUrl());
            $inventoryXmlElement->addAttribute('src-tab', $inventory->getSheetName());

            /** @var StockItem $stockItem */
            foreach ($inventory->getStockItems() as $stockItem) {
                $stockItemXmlElement = $inventoryXmlElement->addChild('StockItem');
                $stockItemXmlElement->addChild('KNumberExists', $stockItem->getKNumberExists());
                $stockItemXmlElement->addChild('KNumber', $stockItem->getKNumber());
                $stockItemXmlElement->addChild('AdditionalKNumbers', $stockItem->getAdditionalKNumbers());
                $stockItemXmlElement->addChild('AlternativeNumber', $stockItem->getAlternativeNumber());
                $stockItemXmlElement->addChild('InventoryContainerID', $stockItem->getInventoryContainerId());
                $stockItemXmlElement->addChild('Name', $stockItem->getName());

                // @todo resolve multiple values and handling here
                $stockItemXmlElement->addChild('Purposes', $stockItem->getPurpose());

                $stockItemXmlElement->addChild('PurposeOther', $stockItem->getPurposeOther());
                $stockItemXmlElement->addChild('InventoryQuantity', $stockItem->getInventoryQuantity());
                $stockItemXmlElement->addChild('HandlingStatus', $stockItem->getHandlingStatus());
                $stockItemXmlElement->addChild(
                    'SupplierRegistrationNumber',
                    $stockItem->getSupplierRegistrationNumber());
            }
        }

        // Format output XML using DOMDocument
        $dom = new DOMDocument("1.0");
        $dom->loadXML($products->asXML());
        $dom->xmlStandalone = true;
        $dom->encoding = 'UTF-8';
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
