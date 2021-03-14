<?php


namespace App\Service\Import;


use App\Exception\ImportException;

class StringImportReader implements ImportReader
{
    /** @var array[] */
    private $csvRows;

    /**
     * StringImportReader constructor.
     * @param string $input
     * @throws ImportException
     */
    public function __construct(string $input)
    {
        $this->csvRows = $this->processCSV($input);
    }

    public function getHeader(): array
    {
        return $this->csvRows[0];
    }

    public function readLines(): \Iterator
    {
        for ($i = 0; $i < count($this->csvRows); ++ $i) {
            yield $this->csvRows[$i];
        }
    }

    /**
     * @param string $csvAsString
     * @return array
     * @throws ImportException
     */
    private function processCSV(string $csvAsString): array
    {
        try {
            return array_map('str_getcsv', explode("\n", $csvAsString));
        } catch (\Exception $e) {
            throw new ImportException('Could not parse CSV: ' . $e->getMessage());
        }
    }
}