<?php

namespace Forikal\GsheetXml\Test;

use Forikal\GsheetXml\Application\Service\GoogleDriveProcessService;
use Forikal\GsheetXml\Application\Service\XmlSerializer;
use Forikal\GsheetXml\Model\Inventory;
use Forikal\GsheetXml\Model\InventoryFactory;
use Forikal\GsheetXml\Model\StockItem;
use PHPUnit\Framework\TestCase;

class InventoryXmlSerializationTest extends TestCase
{
    public function testSingleInventory()
    {
        // Test for GSheet with a single tab
        $inventory = new Inventory();
        $inventory->setSheetName('Sheet 1');
        $inventory->setSpreadsheetUrl('https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit');

        $stockItems = [];
        $stockItem1 = new StockItem();
        $stockItem1->setName('Product 1');
        $stockItem1->setKNumber('111111');
        $stockItem1->setKNumberExists('true');
        $stockItem1->setPurpose('11');
        $stockItem1->setInventoryQuantity('99.99');
        $stockItem1->setAlternativeNumber('');
        $stockItem1->setPurposeOther('1x1x');
        $stockItems[] = $stockItem1;

        $stockItem1 = new StockItem();
        $stockItem1->setName('Product 2');
        $stockItem1->setKNumber('');
        $stockItem1->setKNumberExists('false');
        $stockItem1->setAdditionalKNumbers('55');
        $stockItem1->setPurpose('22,33');
        $stockItem1->setInventoryQuantity('88888');
        $stockItem1->setAlternativeNumber('xx22');
        $stockItem1->setInventoryContainerId('xx33');
        $stockItem1->setPurposeOther('2x2x');
        $stockItem1->setHandlingStatus('HANDLING');
        $stockItem1->setSupplierRegistrationNumber('SUPX');
        $stockItems[] = $stockItem1;

        $inventory->setStockItems($stockItems);

        $serializer = new XmlSerializer();
        $xml = $serializer->serializeSingleProduct([$inventory]);

        $expectedXml = <<<XML
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Products>
  <Product>
    <Inventory src-sheet="https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit" src-tab="Sheet 1">
      <StockItem>
        <KNumberExists>true</KNumberExists>
        <KNumber>111111</KNumber>
        <AdditionalKNumbers></AdditionalKNumbers>
        <AlternativeNumber></AlternativeNumber>
        <InventoryContainerID></InventoryContainerID>
        <Name>Product 1</Name>
        <Purposes>
          <Purpose>11</Purpose>
        </Purposes>
        <PurposeOther>1x1x</PurposeOther>
        <InventoryQuantity>99.99</InventoryQuantity>
        <HandlingStatus></HandlingStatus>
        <SupplierRegistrationNumber></SupplierRegistrationNumber>
      </StockItem>
      <StockItem>
        <KNumberExists>false</KNumberExists>
        <KNumber></KNumber>
        <AdditionalKNumbers>55</AdditionalKNumbers>
        <AlternativeNumber>xx22</AlternativeNumber>
        <InventoryContainerID>xx33</InventoryContainerID>
        <Name>Product 2</Name>
        <Purposes>
          <Purpose>22,33</Purpose>
        </Purposes>
        <PurposeOther>2x2x</PurposeOther>
        <InventoryQuantity>88888</InventoryQuantity>
        <HandlingStatus>HANDLING</HandlingStatus>
        <SupplierRegistrationNumber>SUPX</SupplierRegistrationNumber>
      </StockItem>
    </Inventory>
  </Product>
</Products>

XML;

        $this->assertEquals($expectedXml, $xml);
    }

    public function _testMultipleInventories()
    {
        // Test for GSheet with a multiple tabs

    }


    public function _TestMultipleFiles()
    {
        // Test serialization for multiple files

    }
}
