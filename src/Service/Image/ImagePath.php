<?php


namespace App\Service\Image;


class ImagePath
{
    const SEPARATOR = '://';

    private $type;
    private $path;

    public function __construct(string $type, string $path)
    {
        $this->type = $type;
        $this->path = $path;
    }

    public static function fromFQPath(string $fqPath)
    {
        $parts = explode(ImagePath::SEPARATOR, $fqPath);
        return new ImagePath($parts[0], $parts[1]);
    }

    public function getFQPath(): string
    {
        return $this->type . self::SEPARATOR . $this->path;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}