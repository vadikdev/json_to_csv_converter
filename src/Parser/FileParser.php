<?php

namespace App\Parser;

use Symfony\Component\HttpFoundation\File\File;

class FileParser
{
    /**
     * @param iterable | FileParserInterface[] $parsers
     */
    public function __construct(protected iterable $parsers)
    {}

    public function parse(File $file): iterable
    {
        foreach ($this->parsers as $parser) {
            if ($parser->supports($file)) {
                return $parser->parse($file);
            }
        }

        throw new \LogicException("Could not find parser for given file");
    }
}
