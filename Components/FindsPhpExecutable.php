<?php

namespace HeptacomCliTools\Components;

use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Trait FindsPhpExecutable
 * @package HeptacomCliTools\Components
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

    /**
     * @param array $arguments
     * @return string
     */
    protected static function getPhpCommandline(array $arguments)
    {
        array_unshift($arguments, static::getPhpExecutable());
        array_map('escapeshellarg', $arguments);
        return join(' ', $arguments);
    }
}