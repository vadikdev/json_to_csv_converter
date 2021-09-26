<?php

namespace App\Parser\Parsers;

use App\Parser\FileParserInterface;
use Symfony\Component\HttpFoundation\File\File;

class CsvParser implements FileParserInterface
{
    public function supports(File $file): bool
    {
        return in_array($file->getMimeType(), ['text/csv', 'application/csv']);
    }

    public function parse(File $file): iterable
    {
        $result = [];
        if (($handle = fopen($file->getPathname(), "r")) !== FALSE) {
            $data = [];
            $keys = [];
            while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // First row is key names.
                if (!$keys) {
                    $keys = $row;
                } else {
                    $data[] = $row;
                }
            }
            fclose($handle);

            // Set readable key names for every row.
            $data = array_map(
                fn(array $row) => array_filter(
                    array_combine($keys, $row),
                    // Null values are returned as "" from csv. Filter them.
                    fn ($v) => "" !== $v
                ),
                $data
            );

            $result = self::doParse($data);
        }

        return $result;
    }

    /**
     * Entrypoint to the parser.
     *
     * @param array $data
     * @return array
     */
    protected static function doParse(array $data): array
    {
        if (!$data) {
            return [];
        }

        // Get very first row to start with.
        $item = reset($data);
        list($item, $i) = self::processObject($item, $data);

        return array_merge([$item], self::doParse(array_slice($data, $i)));
    }

    /**
     * Extract array of objects from the next rows.
     *
     * @param array $data
     * @return array
     */
    protected static function extractArray(array $data): array
    {
        if (!$data) {
            return [];
        }

        $item = reset($data);

        // Get the key before last one to set as array identifier.
        $keys = array_keys($item);
        $key = reset($keys);
        $keys = explode('.', $key);
        array_pop($keys);
        $key = array_pop($keys);

        $values = [];
        foreach ($data as $k => $value) {
            if (array_intersect_key($item, $value)) {
                list($_value,) = self::processObject($value, array_slice($data, $k));
                $values[] = $_value;
            }
        }

        return [$key, $values];
    }

    /**
     * @param array $item
     * @param array $data
     * @return array
     */
    protected static function processObject(array $item, array $data): array
    {
        if (!$data) {
            return [$item, 0];
        }

        $i = 0;
        foreach ($data as $value) {
            if ($i > 0 && array_intersect_key($item, $value)) {
                break;
            }
            $i++;
        }

        $item = self::createObjectFromArray($item);
        $_data = array_slice($data,1, $i - 1);
        if ($_data) {
            list($k, $v) = self::extractArray($_data);
            $item->$k = $v;
        }

        // Return object and last processed row number.
        return [$item, $i];
    }

    /**
     * @param array $item
     * @return object
     */
    protected static function createObjectFromArray(array $item): object
    {
        $keys = array_map(function ($key) {
            $keys = explode('.', $key);
            return array_pop($keys);
        }, array_keys($item));

        return (object) array_combine($keys, array_values($item));
    }
}
