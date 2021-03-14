<?php


namespace App\Service\Import;


use App\Service\Import\FieldMapping\ImportMapping;

class ImportPreview
{
    /** @var ImportPreviewRow[] */
    public $rows = [];
    /** @var string[] */
    public $issues = [];

    public $idMapping;
    public $nameMapping;
    public $allianceMapping;
    public $statusMapping;

    public $powerMapping;
    public $highest_powerMapping;
    public $killsMapping;
    public $t1killsMapping;
    public $t2killsMapping;
    public $t3killsMapping;
    public $t4killsMapping;
    public $t5killsMapping;
    public $deadsMapping;
    public $rss_gatheredMapping;
    public $rss_assistanceMapping;
    public $helpsMapping;
    public $rankMapping;
    public $contributionMapping;

    public $snapshot;
    public $addNewGovernors = true;

    public function addRow(ImportPreviewRow $row)
    {
        $this->rows[] = $row;
    }

    public function addIssue(string $issue)
    {
        $this->issues[] = $issue;
    }

    public function setMapping(ImportMapping $mapping)
    {
        foreach (ImportMapping::FIELDS as $field) {
            $this->{$field . 'Mapping'} = $mapping->getMappingForField($field);
        }
    }
}