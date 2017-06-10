<?php

namespace HeptacomCliTools\Components;

use Exception;
use HeptacomCliTools\Components\PluginBuilder\Config;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveRegexIterator;
use RecursiveIteratorIterator;
use RegexIterator;
use SplFileInfo;
use ZipArchive;

/**
 * Class PluginBuilder
 * @package HeptacomCliTools\Components
 */
class PluginBuilder
{
    /**
     * @param Config $config
     * @throws Exception
     */
    public static function build(Config $config)
    {
        if (empty($config->getVersion())) {
            throw new Exception(sprintf('No version was specified in %s', $config->getVersion()));
        }

        $files = new RecursiveDirectoryIterator($config->getPluginDirectory());

        $filesFilter = new RecursiveCallbackFilterIterator($files, function (SplFileInfo $item, $key, $iterator) use ($config) {
            if (in_array($item->getFilename(), $config->getWhitelist())) {
                return true;
            }

            if (in_array($item->getFilename(), $config->getBlacklist())) {
                return false;
            }

            // hide "hidden" files
            return (bool) (strncmp($item->getFilename(), '.', 1) !== 0);
        });

        $listFilesForZip = new RecursiveIteratorIterator($filesFilter);

        $phpFiles = new RegexIterator($listFilesForZip, "/^.+\.php$/i", RecursiveRegexIterator::GET_MATCH);

        if ($config->isLint()) {
            static::tryCall($config->getLintBeginCallback(), iterator_count($phpFiles));

            foreach ($phpFiles as $phpFile) {
                if (!static::lintPHPFile($phpFile[0], $output)) {
                    throw new Exception(sprintf('Syntax error was detected in "%s". See the following error: %s %s', $phpFile[0],PHP_EOL, $output));
                }

                static::tryCall($config->getLintProgressCallback());
            }

            if (!is_file($config->getPluginDirectory()->getPathname() . DIRECTORY_SEPARATOR . $config->getName() . '.php')) {
                throw new Exception('No bootstrap file was found in ' . $config->getPluginDirectory());
            }
            if (!is_file($config->getPluginDirectory()->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml')) {
                throw new Exception('No plugin.xml file was found in ' . $config->getPluginDirectory());
            }

            static::tryCall($config->getLintEndCallback());
        }

        if ($config->isPack()) {
            if (!is_dir($config->getOutputDirectory()->getPathname()) && !mkdir($config->getOutputDirectory()->getPathname(), 0777, true)) {
                throw new Exception(sprintf('Could not create build folder at "%s"'), $config->getOutputDirectory()->getPathname());
            }

            $zipName = implode(DIRECTORY_SEPARATOR, [
                $config->getOutputDirectory()->getPathname(),
                $config->getPluginDirectory()->getBasename() . '_' . $config->getVersion() . '.zip',
            ]);

            if (is_file($zipName) && !unlink($zipName)) {
                throw new Exception(sprintf('Could not remove existing zip archive %s', (new SplFileInfo($zipName))->getBasename()));
            }

            $zip = new ZipArchive();
            $zipMessage = [];

            try {
                if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
                    throw new Exception(sprintf('Could not create zip archive %s', $zipName));
                }

                $listFilesForZip->rewind();
                static::tryCall($config->getPackBeginCallback(), iterator_count($listFilesForZip));

                /** @var SplFileInfo $file */
                foreach ($listFilesForZip as $file) {
                    $localname = $config->getPluginDirectory()->getBasename() . DIRECTORY_SEPARATOR . sscanf($file->getPathname(),$config->getPluginDirectory()->getPathname() . DIRECTORY_SEPARATOR . '%s')[0];

                    if ($zip->addFile($file->getPathname(), $localname)) {
                        $zipMessage[] = sprintf('Added file to zip archive: %s', $localname);
                        static::tryCall($config->getPackProgressCallback());
                    } else {
                        throw new Exception(sprintf('Could not add file to zip archive: %s', $localname));
                    }
                }
            } finally {
                static::tryCall($config->getPackEndCallback(), $zipMessage);
                $zip->close();
            }
        }
    }

    /**
     * @param string $filename
     * @param array $output
     * @return bool
     */
    private static function lintPHPFile($filename, &$output)
    {
        exec(sprintf('"%s" -l "%s"', PHP_BINARY, $filename), $output, $return_var);
        return !$return_var;
    }

    /**
     * @param $callable
     */
    private static function tryCall($callable)
    {
        if (!is_null($callable) && is_callable($callable)) {
            $args = func_get_args();
            array_shift($args);
            call_user_func_array($callable, $args);
        }
    }
}