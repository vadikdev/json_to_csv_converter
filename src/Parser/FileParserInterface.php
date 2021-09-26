<?php

namespace App\Parser;

use Symfony\Component\HttpFoundation\File\File;

interface FileParserInterface
{
    public function supports(File $file): bool;

    public function parse(File $file): iterable;
}
