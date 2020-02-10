<?php declare(strict_types=1);

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

class CreateDirectoryException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $directory;

    public function __construct(SplFileInfo $directory)
    {
        $this->directory = $directory;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Could not create folder: {$this->getDirectory()}";
    }

    /**
     * @return SplFileInfo
     */
    public function getDirectory()
    {
        return $this->directory;
    }
}
