<?php

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;
use SplFileInfo;

/**
 * Class PhpLintException
 * @package HeptacomCliTools\Components\PluginLinter
 */
class PhpLintException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $phpFile;

    /**
     * PhpLintException constructor.
     * @param SplFileInfo $phpFile
     * @param int $message
     */
    public function __construct(SplFileInfo $phpFile, $message)
    {
        parent::__construct($message);
        $this->phpFile = $phpFile;
    }

    /**
     * @return SplFileInfo
     */
    public function getPhpFile()
    {
        return $this->phpFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Syntax error was detected in {$this->getPhpFile()}. See the following error: " . PHP_EOL . " {$this->getMessage()}";
    }
}