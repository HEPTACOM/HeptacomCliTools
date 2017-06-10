<?php

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;
use SplFileInfo;

/**
 * Class MissingPluginMetadataException
 * @package HeptacomCliTools\Components\PluginLinter
 */
class MissingPluginMetadataException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $pluginDirectory;

    /**
     * @var SplFileInfo
     */
    private $pluginMetadataFile;

    /**
     * MissingPluginMetadataException constructor.
     * @param SplFileInfo $pluginDirectory
     * @param SplFileInfo $pluginMetadataFile
     */
    public function __construct(SplFileInfo $pluginDirectory, SplFileInfo $pluginMetadataFile)
    {
        $this->pluginDirectory = $pluginDirectory;
        $this->pluginMetadataFile = $pluginMetadataFile;
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
    public function getPluginMetadataFile()
    {
        return $this->pluginMetadataFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "The plugin file({$this->getPluginMetadataFile()}) in the plugin directory({$this->getPluginDirectory()}) is missing.";
    }
}