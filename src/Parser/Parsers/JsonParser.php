<?php

namespace App\Parser\Parsers;

use App\Parser\FileParserInterface;
use Symfony\Component\HttpFoundation\File\File;

class JsonParser implements FileParserInterface
{
    public function supports(File $file): bool
    {
        return 'application/json' == $file->getMimeType();
    }

    public function parse(File $file): iterable
    {
        return json_decode($file->getContent());
    }
}
