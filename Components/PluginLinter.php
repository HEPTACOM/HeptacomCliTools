<?php

namespace HeptacomCliTools\Components;

use Closure;
use HeptacomCliTools\Components\PluginLinter\MissingBootstrapException;
use HeptacomCliTools\Components\PluginLinter\MissingPluginMetadataException;
use HeptacomCliTools\Components\PluginLinter\MissingVersionException;
use HeptacomCliTools\Components\PluginLinter\PhpLintException;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Class PluginLinter
 * @package HeptacomCliTools\Components
 */
abstract class PluginLinter
{
    /**
     * @var string
     */
    private static $phpExecutable;

    /**
     * @return string
     */
    private static function getPhpExecutable()
    {
        if (is_null(static::$phpExecutable)) {
            static::$phpExecutable = !empty(PHP_BINARY) ? PHP_BINARY : (new PhpExecutableFinder())->find();
        }

        return static::$phpExecutable;
    }

    /**
     * @param PluginData $plugin
     * @param Closure $beginCallback
     * @param Closure $progressCallback
     */
    public static function lint(PluginData $plugin, Closure $beginCallback = null, Closure $progressCallback = null)
    {
        /** @var string[][] $phpFiles */
        $phpFiles = new RegexIterator($plugin->getFiles(), "/^.+\.php$/i", RecursiveRegexIterator::GET_MATCH);

        if (!is_null($beginCallback) && is_callable($beginCallback)) {
            $beginCallback(iterator_count($phpFiles));
        }

        foreach ($phpFiles as $phpFile) {
            if (!static::lintPHPFile($phpFile[0], $output)) {
                throw new PhpLintException(new SplFileInfo($phpFile[0]), $output);
            }

            if (!is_null($progressCallback) && is_callable($progressCallback)) {
                $progressCallback();
            }
        }

        if (!$plugin->getBootstrap()->isFile()) {
            throw new MissingBootstrapException($plugin->getDirectory(), $plugin->getBootstrap());
        }

        if (!$plugin->getPluginXml()->isFile()) {
            throw new MissingPluginMetadataException($plugin->getDirectory(), $plugin->getPluginXml());
        }

        if (empty($plugin->getVersion()) || $plugin->getVersion() === 'undefined') {
            throw new MissingVersionException();
        }
    }

    /**
     * @param string $filename
     * @param array $output
     * @return bool
     */
    private static function lintPHPFile($filename, &$output)
    {
        exec(sprintf('%s -l %s', escapeshellarg(static::getPhpExecutable()), escapeshellarg($filename)), $output, $return_var);
        return !$return_var;
    }
}