<?php


namespace App\Service\Import;


use App\Exception\ImportException;

class ImportPreviewRow
{
    public $id;
    public $name;
    public $power;
    public $highest_power;
    public $kills;
    public $t1kills;
    public $t2kills;
    public $t3kills;
    public $t4kills;
    public $t5kills;
    public $deads;
    public $rss_gathered;
    public $rss_assistance;
    public $helps;
    public $rank;
    public $contribution;

    public $alliance;

    /**
     * ImportPreviewRow constructor.
     * @param array $rowData
     * @param ImportMapping $mapping
     * @throws ImportException
     */
    public function __construct(array $rowData, ImportMapping $mapping)
    {
        $this->setFieldValue($rowData, $mapping, ImportMapping::FIELD_ID);

        if (!$this->id) {
            throw new ImportException('Could not find governor id!');
        }

        foreach (ImportMapping::FIELDS as $field) {
            $this->setFieldValue($rowData, $mapping, $field);
        }
    }

    private function setFieldValue(array $rowData, ImportMapping $mapping, string $field)
    {
        $index = $mapping->getIndexForField($field);
        $this->{$field} = is_int($index) ? $rowData[$index] : null;
    }
}