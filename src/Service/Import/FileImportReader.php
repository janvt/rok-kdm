<?php


namespace App\Service\Import;


use App\Exception\ImportException;

class FileImportReader implements ImportReader
{
    /** @var string */
    private $filePath;

    /**
     * @param string $filePath
     * @throws ImportException
     */
    public function __construct(string $filePath)
    {
        if (!\file_exists($filePath)) {
            throw new ImportException('File not found: ' . $filePath);
        }

        $this->filePath = $filePath;
    }

    public function getHeader(): array
    {
        $handle = \fopen($this->filePath, 'r');
        $header = \fgetcsv($handle);
        \fclose($handle);

        return $header;
    }

    public function readLines(): \Iterator
    {
        $handle = \fopen($this->filePath, 'r');
        \fgetcsv($handle); // pop header

        while($row = \fgetcsv($handle)) {
            yield $row;
        }

        \fclose($handle);
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