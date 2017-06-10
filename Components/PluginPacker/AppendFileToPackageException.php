<?php

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

/**
 * Class AppendFileToPackageException
 * @package HeptacomCliTools\Components\PluginPacker
 */
class AppendFileToPackageException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $packageFile;

    /**
     * @var SplFileInfo
     */
    private $addFile;

    /**
     * AppendFileToPackageException constructor.
     * @param SplFileInfo $packageFile
     * @param SplFileInfo $addFile
     */
    public function __construct(SplFileInfo $packageFile, SplFileInfo $addFile)
    {
        $this->packageFile = $packageFile;
        $this->addFile = $addFile;
    }

    /**
     * @return SplFileInfo
     */
    public function getPackageFile()
    {
        return $this->packageFile;
    }

    /**
     * @return SplFileInfo
     */
    public function getAddFile()
    {
        return $this->addFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Could not add file({$this->getAddFile()}) to package: {$this->getPackageFile()}";
    }
}