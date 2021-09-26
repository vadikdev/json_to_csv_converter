<?php

namespace App\FileCreator\Creators;

use App\FileCreator\FileCreatorInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class JsonCreator implements FileCreatorInterface
{
    public function supports(string $mime): bool
    {
        return 'application/json' == $mime;
    }

    public function create(iterable $data): BinaryFileResponse
    {
        $dir = sys_get_temp_dir();
        $file = $dir . '/' . uniqid();
        file_put_contents($file, json_encode($data));
        return (new BinaryFileResponse($file, 200, ['headers' => [
                'Content-type' => 'application/json'
            ]]))
            ->deleteFileAfterSend(true)
            ->setContentDisposition(
                ResponseHeaderBag::DISPOSITION_ATTACHMENT,
                'data.json'
            );
    }
}
