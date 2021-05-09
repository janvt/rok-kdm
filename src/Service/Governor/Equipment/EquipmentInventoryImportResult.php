<?php


namespace App\Service\Governor\Equipment;


class EquipmentInventoryImportResult
{
    private $rowsImported = 0;
    private $invalidRows = 0;

    public function addRowImported()
    {
        ++ $this->rowsImported;
    }

    public function addInvalidRow()
    {
        ++ $this->invalidRows;
    }

    public function getRowsImported(): int
    {
        return $this->rowsImported;
    }

    public function getInvalidRows(): int
    {
        return $this->invalidRows;
    }
}