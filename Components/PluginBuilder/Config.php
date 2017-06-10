<?php

namespace HeptacomCliTools\Components\PluginBuilder;

use Closure;
use HeptacomCliTools\Components\PluginData;
use SplFileInfo;

/**
 * Class Config
 * @package HeptacomCliTools\Components\PluginBuilder
 */
class Config
{
    /**
     * @var PluginData
     */
    private $plugin;

    /**
     * @var SplFileInfo
     */
    private $outputDirectory;

    /**
     * @var Closure
     */
    private $packBeginCallback;

    /**
     * @var Closure
     */
    private $packProgressCallback;

    /**
     * @var Closure
     */
    private $packEndCallback;

    /**
     * @return Config
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return PluginData
     */
    public function getPlugin()
    {
        return $this->plugin;
    }

    /**
     * @param PluginData $plugin
     * @return Config
     */
    public function setPlugin($plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * @return SplFileInfo
     */
    public function getOutputDirectory()
    {
        return $this->outputDirectory;
    }

    /**
     * @param SplFileInfo $outputDirectory
     * @return Config
     */
    public function setOutputDirectory(SplFileInfo $outputDirectory)
    {
        $this->outputDirectory = $outputDirectory;
        return $this;
    }

    /**
     * @return Closure
     */
    public function getPackBeginCallback()
    {
        return $this->packBeginCallback;
    }

    /**
     * @param Closure $packBeginCallback
     * @return Config
     */
    public function setPackBeginCallback($packBeginCallback)
    {
        $this->packBeginCallback = $packBeginCallback;
        return $this;
    }

    /**
     * @return Closure
     */
    public function getPackProgressCallback()
    {
        return $this->packProgressCallback;
    }

    /**
     * @param Closure $packProgressCallback
     * @return Config
     */
    public function setPackProgressCallback($packProgressCallback)
    {
        $this->packProgressCallback = $packProgressCallback;
        return $this;
    }

    /**
     * @return Closure
     */
    public function getPackEndCallback()
    {
        return $this->packEndCallback;
    }

    /**
     * @param Closure $packEndCallback
     * @return Config
     */
    public function setPackEndCallback($packEndCallback)
    {
        $this->packEndCallback = $packEndCallback;
        return $this;
    }
}