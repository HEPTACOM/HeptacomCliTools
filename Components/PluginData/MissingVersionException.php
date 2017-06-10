<?php

namespace HeptacomCliTools\Components\PluginData;

use Exception;

/**
 * Class MissingVersionException
 * @package HeptacomCliTools\Components\PluginData
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