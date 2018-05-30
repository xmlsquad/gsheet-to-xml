<?php

namespace Forikal\GsheetXml\Model;

class StockItem
{
    /** @var string|null */
    private $kNumber;

    /** @var string|null */
    private $kNumberExists;

    /** @var string|null */
    private $additionalKNumbers;

    /** @var string|null */
    private $alternativeNumber;

    /** @var string|null */
    private $inventoryContainerId;

    /** @var string|null */
    private $name;

    /** @var string|null */
    private $purpose;

    /** @var string|null */
    private $purposeOther;

    /** @var string|null */
    private $inventoryQuantity;

    /** @var string|null */
    private $handlingStatus;

    /** @var string|null */
    private $supplierRegistrationNumber;

    public function getKNumber(): ?string
    {
        return $this->kNumber;
    }

    public function setKNumber(?string $kNumber): void
    {
        $this->kNumber = $kNumber;
    }

    public function getKNumberExists(): ?string
    {
        return $this->kNumberExists;
    }

    public function setKNumberExists(?string $kNumberExists): void
    {
        $this->kNumberExists = $kNumberExists;
    }

    public function getAdditionalKNumbers(): ?string
    {
        return $this->additionalKNumbers;
    }

    public function setAdditionalKNumbers(?string $additionalKNumbers): void
    {
        $this->additionalKNumbers = $additionalKNumbers;
    }

    public function getAlternativeNumber(): ?string
    {
        return $this->alternativeNumber;
    }

    public function setAlternativeNumber(?string $alternativeNumber): void
    {
        $this->alternativeNumber = $alternativeNumber;
    }

    public function getInventoryContainerId(): ?string
    {
        return $this->inventoryContainerId;
    }

    public function setInventoryContainerId(?string $inventoryContainerId): void
    {
        $this->inventoryContainerId = $inventoryContainerId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPurpose(): ?string
    {
        return $this->purpose;
    }

    public function setPurpose(?string $purpose): void
    {
        $this->purpose = $purpose;
    }

    public function getPurposeOther(): ?string
    {
        return $this->purposeOther;
    }

    public function setPurposeOther(?string $purposeOther): void
    {
        $this->purposeOther = $purposeOther;
    }

    public function getInventoryQuantity(): ?string
    {
        return $this->inventoryQuantity;
    }

    public function setInventoryQuantity(?string $inventoryQuantity): void
    {
        $this->inventoryQuantity = $inventoryQuantity;
    }

    public function getHandlingStatus(): ?string
    {
        return $this->handlingStatus;
    }

    public function setHandlingStatus(?string $handlingStatus): void
    {
        $this->handlingStatus = $handlingStatus;
    }

    public function getSupplierRegistrationNumber(): ?string
    {
        return $this->supplierRegistrationNumber;
    }

    public function setSupplierRegistrationNumber(?string $supplierRegistrationNumber): void
    {
        $this->supplierRegistrationNumber = $supplierRegistrationNumber;
    }
}