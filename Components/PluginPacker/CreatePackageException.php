<?php

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

/**
 * Class CreatePackageException
 * @package HeptacomCliTools\Components\PluginPacker
 */
class CreatePackageException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $packageFile;

    /**
     * CreatePackageException constructor.
     * @param SplFileInfo $packageFile
     */
    public function __construct(SplFileInfo $packageFile)
    {
        $this->packageFile = $packageFile;
    }

    /**
     * @return SplFileInfo
     */
    public function getPackageFile()
    {
        return $this->packageFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Could not create zip archive {$this->getPackageFile()}";
    }
}