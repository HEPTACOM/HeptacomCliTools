<?php

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;

/**
 * Class MissingVersionException
 * @package HeptacomCliTools\Components\PluginLinter
 */
class MissingVersionException extends Exception
{
    /**
     * @return string
     */
    public function __toString()
    {
        return "The version information is not preset.";
    }
}