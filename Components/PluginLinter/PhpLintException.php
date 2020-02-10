<?php declare(strict_types=1);

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;
use SplFileInfo;

class PhpLintException extends Exception
{
    /**
     * @var SplFileInfo
     */
    private $phpFile;

    /**
     * @param int $message
     */
    public function __construct(SplFileInfo $phpFile, $message)
    {
        parent::__construct($message);
        $this->phpFile = $phpFile;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return "Syntax error was detected in {$this->getPhpFile()}. See the following error: " . PHP_EOL . " {$this->getMessage()}";
    }

    /**
     * @return SplFileInfo
     */
    public function getPhpFile()
    {
        return $this->phpFile;
    }
}
