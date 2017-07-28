<?php

namespace HeptacomAmp\Components;

use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Trait FindsPhpExecutable
 * @package HeptacomAmp\Components
 */
trait FindsPhpExecutable
{
    /**
     * @var string
     */
    private static $phpExecutable;

    /**
     * @return string
     */
    protected static function getPhpExecutable()
    {
        if (is_null(static::$phpExecutable)) {
            static::$phpExecutable = !empty(PHP_BINARY) ? PHP_BINARY : (new PhpExecutableFinder())->find();
        }

        return static::$phpExecutable;
    }
}