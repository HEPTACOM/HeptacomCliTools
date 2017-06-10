<?php

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;
use SplFileInfo;

/**
 * Class MissingBootstrapException
 * @package HeptacomCliTools\Components\PluginLinter
 */
class MissingBootstrapException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $pluginDirectory;

    /**
     * @var SplFileInfo
     */
    private $bootstrapFile;

    /**
     * @return SplFileInfo
     */
    public function getPluginDirectory()
    {
        return $this->pluginDirectory;
    }

    /**
     * @return SplFileInfo
     */
    public function getBootstrapFile()
    {
        return $this->bootstrapFile;
    }

    /**
     * MissingBootstrapException constructor.
     * @param SplFileInfo $pluginDirectory
     * @param SplFileInfo $bootstrapFile
     */
    public function __construct(SplFileInfo $pluginDirectory, SplFileInfo $bootstrapFile)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->bootstrapFile = $bootstrapFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "The bootstrap file({$this->getBootstrapFile()}) in the plugin directory({$this->getPluginDirectory()}) is missing.";
    }
}