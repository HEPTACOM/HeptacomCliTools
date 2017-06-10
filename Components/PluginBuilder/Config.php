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
     * @var bool
     */
    private $lint;

    /**
     * @var bool
     */
    private $pack;

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
     * @var Closure
     */
    private $lintBeginCallback;

    /**
     * @var Closure
     */
    private $lintProgressCallback;

    /**
     * @var Closure
     */
    private $lintEndCallback;

    /**
     * @return Config
     */
    public static function create()
    {
        return new static();
    }

    /**
     * @return bool
     */
    public function isLint()
    {
        return $this->lint;
    }

    /**
     * @param bool $lint
     * @return Config
     */
    public function setLint($lint)
    {
        $this->lint = $lint;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPack()
    {
        return $this->pack;
    }

    /**
     * @param bool $pack
     * @return Config
     */
    public function setPack($pack)
    {
        $this->pack = $pack;
        return $this;
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

    /**
     * @return Closure
     */
    public function getLintBeginCallback()
    {
        return $this->lintBeginCallback;
    }

    /**
     * @param Closure $lintBeginCallback
     * @return Config
     */
    public function setLintBeginCallback($lintBeginCallback)
    {
        $this->lintBeginCallback = $lintBeginCallback;
        return $this;
    }

    /**
     * @return Closure
     */
    public function getLintProgressCallback()
    {
        return $this->lintProgressCallback;
    }

    /**
     * @param Closure $lintProgressCallback
     * @return Config
     */
    public function setLintProgressCallback($lintProgressCallback)
    {
        $this->lintProgressCallback = $lintProgressCallback;
        return $this;
    }

    /**
     * @return Closure
     */
    public function getLintEndCallback()
    {
        return $this->lintEndCallback;
    }

    /**
     * @param Closure $lintEndCallback
     * @return Config
     */
    public function setLintEndCallback($lintEndCallback)
    {
        $this->lintEndCallback = $lintEndCallback;
        return $this;
    }
}