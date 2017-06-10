<?php

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

/**
 * Class RemovePackageException
 * @package HeptacomCliTools\Components\PluginPacker
 */
class RemovePackageException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $packageFile;

    /**
     * RemovePackageException constructor.
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
        return "Could not remove existing package: {$this->getPackageFile()}";
    }
}