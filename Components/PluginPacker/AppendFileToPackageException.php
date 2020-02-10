<?php declare(strict_types=1);

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

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

    public function __construct(SplFileInfo $packageFile, SplFileInfo $addFile)
    {
        $this->packageFile = $packageFile;
        $this->addFile = $addFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Could not add file({$this->getAddFile()}) to package: {$this->getPackageFile()}";
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
}
