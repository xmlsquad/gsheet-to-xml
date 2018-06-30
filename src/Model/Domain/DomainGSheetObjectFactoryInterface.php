<?php
/**
 * Created by PhpStorm.
 * User: jw
 * Date: 30/06/2018
 * Time: 15:23
 */

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