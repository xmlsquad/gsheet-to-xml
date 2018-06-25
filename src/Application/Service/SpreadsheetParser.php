<?php

namespace XmlSquad\GsheetXml\Application\Service;

class SpreadsheetParser
{
    public function parse()
    {
        $sheets = $this->parseSheets();
    }

}