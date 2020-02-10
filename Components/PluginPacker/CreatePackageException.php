<?php declare(strict_types=1);

namespace HeptacomCliTools\Components\PluginPacker;

use Exception;
use SplFileInfo;

class CreatePackageException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $packageFile;

    public function __construct(SplFileInfo $packageFile)
    {
        $this->packageFile = $packageFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Could not create zip archive {$this->getPackageFile()}";
    }

    /**
     * @return SplFileInfo
     */
    public function getPackageFile()
    {
        return $this->packageFile;
    }
}
