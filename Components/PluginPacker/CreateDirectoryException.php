<?php

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

/**
 * Class CreateDirectoryException
 * @package HeptacomCliTools\Components\PluginPacker
 */
class CreateDirectoryException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $directory;

    /**
     * @return SplFileInfo
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * CreateDirectoryException constructor.
     * @param SplFileInfo $directory
     */
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
}