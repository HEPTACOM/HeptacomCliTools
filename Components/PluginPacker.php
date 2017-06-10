<?php

namespace HeptacomCliTools\Components;

use Closure;
use HeptacomCliTools\Components\PluginPacker\AppendFileToPackageException;
use HeptacomCliTools\Components\PluginPacker\CreateDirectoryException;
use HeptacomCliTools\Components\PluginPacker\CreatePackageException;
use HeptacomCliTools\Components\PluginPacker\RemovePackageException;
use SplFileInfo;
use ZipArchive;

/**
 * Class PluginPacker
 * @package HeptacomCliTools\Components
 */
class PluginPacker
{
    /**
     * @param PluginData $plugin
     * @param SplFileInfo $outputDirectory
     * @param Closure $beginCallback
     * @param Closure $progressCallback
     * @param Closure $endCallback
     * @return SplFileInfo
     * @throws AppendFileToPackageException
     * @throws CreateDirectoryException
     * @throws CreatePackageException
     * @throws RemovePackageException
     */
    public static function pack(PluginData $plugin, SplFileInfo $outputDirectory, Closure $beginCallback = null, Closure $progressCallback = null, Closure $endCallback = null)
    {
        $files = $plugin->getFiles();

        if (!is_dir($outputDirectory->getPathname()) && !mkdir($outputDirectory->getPathname(), 0777, true)) {
            throw new CreateDirectoryException($outputDirectory);
        }

        $zipName = new SplFileInfo(implode(DIRECTORY_SEPARATOR, [
            $outputDirectory->getPathname(),
            $plugin->getName() . '_' . $plugin->getVersion() . '.zip',
        ]));

        if ($zipName->isFile() && unlink($zipName->getPathname())) {
            throw new RemovePackageException($zipName);
        }

        $zip = new ZipArchive();
        $zipMessage = [];

        try {
            if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
                throw new CreatePackageException($zipName);
            }

            if (!is_null($beginCallback) && is_callable($beginCallback)) {
                $beginCallback(iterator_count($files));
            }

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $localname = $plugin->getName() . DIRECTORY_SEPARATOR . sscanf($file->getPathname(),$plugin->getDirectory()->getPathname() . DIRECTORY_SEPARATOR . '%s')[0];

                if ($zip->addFile($file->getPathname(), $localname)) {
                    $zipMessage[] = sprintf('Added file to zip archive: %s', $localname);
                    if (!is_null($progressCallback) && is_callable($progressCallback)) {
                        $progressCallback();
                    }
                } else {
                    throw new AppendFileToPackageException($zipName, $file);
                }
            }
        } finally {
            if (!is_null($endCallback) && is_callable($endCallback)) {
                $endCallback($zipMessage);
            }
            $zip->close();
        }

        return $zipName;
    }
}