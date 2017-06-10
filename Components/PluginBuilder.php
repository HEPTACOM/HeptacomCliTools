<?php

namespace HeptacomCliTools\Components;

use Exception;
use HeptacomCliTools\Components\PluginBuilder\Config;
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
        $files = $config->getPlugin()->getFiles();

        if (!is_dir($config->getOutputDirectory()->getPathname()) && !mkdir($config->getOutputDirectory()->getPathname(), 0777, true)) {
            throw new Exception(sprintf('Could not create build folder at "%s"'), $config->getOutputDirectory()->getPathname());
        }

        $zipName = implode(DIRECTORY_SEPARATOR, [
            $config->getOutputDirectory()->getPathname(),
            $config->getPlugin()->getName() . '_' . $config->getPlugin()->getVersion() . '.zip',
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

            $files->rewind();
            static::tryCall($config->getPackBeginCallback(), iterator_count($files));

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $localname = $config->getPlugin()->getName() . DIRECTORY_SEPARATOR . sscanf($file->getPathname(),$config->getPlugin()->getDirectory()->getPathname() . DIRECTORY_SEPARATOR . '%s')[0];

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