<?php

namespace XmlSquad\GsheetXml\Model\Domain;


interface DomainGSheetObjectFactoryInterface
{

    /**
     * Creates DomainGSheetObject; The domain object that is represented by a GSheet.
     *
     *
     * @param array $data
     * @param string $spreadsheetUrl
     * @return DomainGSheetObject
     */
    public function createDomainGSheetObject(array $data, string $spreadsheetUrl);
}