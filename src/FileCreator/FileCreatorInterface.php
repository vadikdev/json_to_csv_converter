<?php

namespace App\FileCreator;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

interface FileCreatorInterface
{
    public function supports(string $mime): bool;

    public function create(iterable $data): BinaryFileResponse;
}
