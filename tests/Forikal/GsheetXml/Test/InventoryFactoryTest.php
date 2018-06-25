<?php

namespace XmlSquad\GsheetXml\Test;

use XmlSquad\GsheetXml\Model\Inventory;
use XmlSquad\GsheetXml\Model\InventoryFactory;
use XmlSquad\GsheetXml\Model\StockItem;
use PHPUnit\Framework\TestCase;

class InventoryFactoryTest extends TestCase
{
    public function testInventoryFactory()
    {
        $factory = new InventoryFactory();
        $url = 'https://docs.google.com/spreadsheets/d/xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx/edit';

        $data = [
            'title'  => 'Spreadsheet title',
            'values' => [
                [
                    'Name'              => 'Product 1',
                    'KNumber'           => '1',
                    'KNumberExists'     => 'true',
                    'Quantity'          => '11',
                    'AlternativeNumber' => '111',
                    'Purpose'           => '1111',
                    'PurposeOther'      => '11111',
                ],
                [
                    'Name'              => 'Product 2',
                    'KNumber'           => '',
                    'KNumberExists'     => 'false',
                    'Quantity'          => '22',
                    'AlternativeNumber' => '222',
                    'Purpose'           => '2222',
                    'PurposeOther'      => '22222',
                ],
            ],
        ];

        $inventory = $factory->make($data, $url);
        $this->assertCount(2, $inventory->getStockItems());

        /** @var StockItem $stockItem1 */
        $stockItem1 = $inventory->getStockItems()[0];
        $this->assertEquals('Product 1', $stockItem1->getName());
        $this->assertEquals('1', $stockItem1->getKNumber());
        $this->assertEquals('true', $stockItem1->getKNumberExists());
        $this->assertEquals('11', $stockItem1->getInventoryQuantity());
        $this->assertEquals('111', $stockItem1->getAlternativeNumber());
        $this->assertEquals('1111', $stockItem1->getPurpose());
        $this->assertEquals('11111', $stockItem1->getPurposeOther());

        /** @var StockItem $stockItem1 */
        $stockItem1 = $inventory->getStockItems()[1];
        $this->assertEquals('Product 2', $stockItem1->getName());
        $this->assertEquals('', $stockItem1->getKNumber());
        $this->assertEquals('false', $stockItem1->getKNumberExists());
        $this->assertEquals('22', $stockItem1->getInventoryQuantity());
        $this->assertEquals('222', $stockItem1->getAlternativeNumber());
        $this->assertEquals('2222', $stockItem1->getPurpose());
        $this->assertEquals('22222', $stockItem1->getPurposeOther());

        $this->assertInstanceOf(Inventory::class, $inventory);
    }
}
