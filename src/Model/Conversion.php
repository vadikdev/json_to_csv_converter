<?php

namespace App\Model;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class Conversion
{
    /**
     * @Assert\File()
     * @var UploadedFile
     */
    protected UploadedFile | null $file = null;

    /**
     * @var string|null
     * @Assert\NotBlank()
     */
    protected string | null $mime = null;

    /**
     * @return UploadedFile|null
     */
    public function getFile(): ?UploadedFile
    {
        return $this->file;
    }

    /**
     * @param UploadedFile|null $file
     */
    public function setFile(?UploadedFile $file): void
    {
        $this->file = $file;
    }

    /**
     * @return string|null
     */
    public function getMime(): ?string
    {
        return $this->mime;
    }

    /**
     * @param string|null $mime
     */
    public function setMime(?string $mime): void
    {
        $this->mime = $mime;
    }
}
