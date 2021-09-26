<?php

namespace App\FileCreator;

use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileCreator
{
    /**
     * @param iterable | FileCreatorInterface[] $creators
     */
    public function __construct(protected iterable $creators)
    {}

    public function create(string $mime, iterable $data): BinaryFileResponse
    {
        foreach ($this->creators as $creator) {
            if ($creator->supports($mime)) {
                return $creator->create($data);
            }
        }

        throw new \LogicException(sprintf("Could not find file creator for your mime %s", $mime));
    }
}
