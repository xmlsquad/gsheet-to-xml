<?php

namespace XmlSquad\GsheetXml\Model\Service;


interface XmlSerializerInterface
{
    /**
     *
     *
     * @param array $domainGSheetObjects
     * @return mixed
     */
    public function serializeDomainGSheetObjects(array $domainGSheetObjects);
}