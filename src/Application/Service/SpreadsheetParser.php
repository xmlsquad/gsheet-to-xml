<?php

namespace Forikal\GsheetXml\Application\Service;

class SpreadsheetParser
{
    public function parse()
    {
        $sheets = $this->parseSheets();
    }

}