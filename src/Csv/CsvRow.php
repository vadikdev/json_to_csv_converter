<?php

namespace App\Csv;

class CsvRow
{
    protected array $values = [];

    public function setValue(string $key, $value): void
    {
        $this->values[$key] = $value;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    public function getKeys(): array
    {
        return array_keys($this->values);
    }

    public function getValue(string $key)
    {
        return $this->values[$key] ?? null;
    }
}
