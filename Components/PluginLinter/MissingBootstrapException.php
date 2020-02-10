<?php declare(strict_types=1);

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;
use SplFileInfo;

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
}
