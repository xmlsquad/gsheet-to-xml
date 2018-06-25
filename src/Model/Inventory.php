<?php

namespace XmlSquad\GsheetXml\Model;

class Inventory
{
    /** @var string|null */
    private $spreadsheetUrl;

    /** @var string|null */
    private $sheetName;

    /** @var string|null */
    private $spreadsheetName;

    /** @var array */
    private $stockItems = [];

    public function getSpreadsheetUrl(): ?string
    {
        return $this->spreadsheetUrl;
    }

    public function setSpreadsheetUrl(?string $spreadsheetUrl): void
    {
        $this->spreadsheetUrl = $spreadsheetUrl;
    }

    public function getSheetName(): ?string
    {
        return $this->sheetName;
    }

    public function setSheetName(?string $sheetName): void
    {
        $this->sheetName = $sheetName;
    }

    public function getStockItems(): array
    {
        return $this->stockItems;
    }

    public function setStockItems(array $stockItems): void
    {
        $this->stockItems = $stockItems;
    }

    public function getSpreadsheetName(): ?string
    {
        return $this->spreadsheetName;
    }

    public function setSpreadsheetName(?string $spreadsheetName): void
    {
        $this->spreadsheetName = $spreadsheetName;
    }
}
