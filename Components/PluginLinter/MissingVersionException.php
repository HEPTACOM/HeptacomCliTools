<?php declare(strict_types=1);

namespace HeptacomCliTools\Components\PluginLinter;

use Exception;

class MissingVersionException extends Exception
{
    /**
     * @return string
     */
    public function __toString()
    {
        return 'The version information is not preset.';
    }
}
