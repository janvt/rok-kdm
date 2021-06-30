<?php


namespace App\Service\Import\FieldMapping;


class ImportMappingGuesser
{
    private $header;

    public function __construct(array $header)
    {
        $this->header = $header;
    }

    public function getMappings(): array
    {
        $mappings = [];

        foreach (ImportMapping::FIELDS as $field) {
            $foundField = $this->findFieldInHeader($this->header, $field);

            if (!$foundField) {
                $foundField = $this->findFieldInHeader(
                    $this->header,
                    str_replace('_', '', $field)
                );
            }

            if ($foundField) {
                $mappings[$field] = $foundField;
            }
        }

        return $mappings;
    }

    private function findFieldInHeader(array $header, string $field): ?string
    {
        foreach ($header as $headerField) {
            $sanitizedHeaderField = \str_replace(' ', '', strtolower($headerField));

            if ($this->fieldMatches($field, $sanitizedHeaderField)) {
                return $headerField;
            }

            foreach ($this->getAlternativesForField($field) as $fieldAlternative) {
                if ($this->fieldMatches($fieldAlternative, $sanitizedHeaderField)) {
                    return $headerField;
                }
            }
        }

        return null;
    }

    private function fieldMatches(string $field, string $sanitizedHeaderField): bool
    {
        $found = strpos($sanitizedHeaderField, $field) !== false;

        if ($field === ImportMapping::FIELD_KILLS) {
            // make sure "kills" is found cleanly and does not match something like "T4 kills"
            foreach (['t1', 't2', 't3', 't4', 't5'] as $tx) {
                if (strpos($sanitizedHeaderField, $tx) !== false) {
                    return false;
                }
            }
        }

        return $found;
    }

    private function getAlternativesForField(string $field): array
    {
        if ($field === ImportMapping::FIELD_ID) {
            return ['govid', 'governorid', 'rokid', 'playerid'];
        }

        if ($field === ImportMapping::FIELD_NAME) {
            return ['govname', 'governorname', 'playername'];
        }

        if ($field === ImportMapping::FIELD_KILLS) {
            return ['totalkills'];
        }

        if ($field === ImportMapping::FIELD_DEADS) {
            return ['dead', 'deaths'];
        }

        if ($field === ImportMapping::FIELD_RSS_ASSISTANCE) {
            return ['rssdonation', 'rssdonations'];
        }

        return [];
    }
}