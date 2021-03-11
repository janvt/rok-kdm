<?php


namespace App\Service\Import;


use Symfony\Component\Form\FormInterface;

class ImportMapping
{
    const FIELD_ID = 'id';
    const FIELD_NAME = 'name';
    const FIELD_DEADS = 'deads';
    const FIELD_KILLS = 'kills';
    const FIELD_RSS_ASSISTANCE = 'rss_assistance';
    const FIELDS = [
        self::FIELD_ID,
        self::FIELD_NAME,
        'status',
        'alliance',
        'power',
        'highest_power',
        self::FIELD_DEADS,
        self::FIELD_KILLS,
        't1kills',
        't2kills',
        't3kills',
        't4kills',
        't5kills',
        'rss_gathered',
        self::FIELD_RSS_ASSISTANCE,
        'helps',
        'rank',
        'contribution',
    ];

    private $mappings = [];
    private $header = [];

    public function __construct(?string $mappings)
    {
        if ($mappings) {
            $this->mappings = json_decode($mappings);
        }
    }

    public static function fromForm(FormInterface $form): ImportMapping
    {
        $mappings = [];
        foreach(self::FIELDS as $field) {
            $mappings[$field] = $form->get($field . 'Mapping')->getData();
        }

        return new self(json_encode($mappings));
    }

    public function __toString(): string
    {
        return json_encode($this->mappings);
    }

    public function getMappingForField(string $name)
    {
        return isset($this->mappings->{$name}) ? $this->mappings->{$name} : null;
    }

    public function getIndexForField(string $name)
    {
        $mappingValue = $this->getMappingForField($name);
        if ($mappingValue) {
            return array_search($mappingValue, $this->header);
        }

        return null;
    }

    public function setHeader(array $header)
    {
        $this->header = $header;
    }
}