<?php

namespace App\FileCreator\Creators;

use App\Csv\CsvRow;
use App\FileCreator\FileCreatorInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class CsvCreator implements FileCreatorInterface
{
    public function supports(string $mime): bool
    {
        return 'text/csv' == $mime;
    }

    public function create(iterable $data): BinaryFileResponse
    {
        $dir = sys_get_temp_dir();
        $file = $dir . '/' . uniqid();
        $resource = fopen($file, 'w+');
        $first = true;

        foreach ($data as $value) {
            $rows = self::visitObject(new CsvRow(), $value);
            $keys = array_unique(array_merge(...array_map(fn(CsvRow $row) => $row->getKeys(), $rows)));

            if ($first) {
                fputcsv($resource, $keys);
                $first = false;
            }

            foreach ($rows as $row) {
                $_values = array_map(fn(string $key) => $row->getValue($key), $keys);
                fputcsv($resource, $_values);
            }
        }

        return (new BinaryFileResponse($file, 200, ['headers' => [
                'Content-type' => 'text/csv'
            ]]))
            ->deleteFileAfterSend(true)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'data.csv'
            );
    }

    /**
     * @param CsvRow $row
     * @param \stdClass $object
     * @param array $path
     * @return array | CsvRow[]
     */
    protected static function visitObject(CsvRow $row, \stdClass $object, array $path = []): array
    {
        $_path = $path;

        $rows = [];
        foreach (get_object_vars($object) as $key => $value) {
            if (is_array($value)) {
                $_path[] = $key;
                $rows = self::visitArray($row, $value, $_path);
            } else {
                $row->setValue($path ? implode('.', $path) . '.' . $key : $key, $value);
            }
        }

        array_unshift($rows, $row);
        return $rows;
    }

    protected static function visitArray(CsvRow $row, array $data, array $path = []): array
    {
        $rows = [];

        foreach ($data as $key => $value) {
            //$_row = clone $row;
            $_row = new CsvRow();
            if (is_array($value)) {
                $rows = self::visitArray($_row, $value, $path);
            } else {
                $_path[] = $key;
                $rows = array_merge($rows, self::visitObject($_row, $value, $path));;
            }
        }

        return $rows;
    }
}
