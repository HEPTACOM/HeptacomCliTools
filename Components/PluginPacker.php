<?php declare(strict_types=1);

namespace HeptacomCliTools\Components;

use Closure;
use HeptacomCliTools\Components\PluginPacker\AppendFileToPackageException;
use HeptacomCliTools\Components\PluginPacker\CreateDirectoryException;
use HeptacomCliTools\Components\PluginPacker\CreatePackageException;
use HeptacomCliTools\Components\PluginPacker\RemovePackageException;
use SplFileInfo;
use ZipArchive;

class PluginPacker
{
    /**
     * @param Closure $beginCallback
     * @param Closure $progressCallback
     * @param Closure $endCallback
     *
     * @throws AppendFileToPackageException
     * @throws CreateDirectoryException
     * @throws CreatePackageException
     * @throws RemovePackageException
     *
     * @return SplFileInfo
     */
    public static function pack(PluginData $plugin, SplFileInfo $outputDirectory, Closure $beginCallback = null, Closure $progressCallback = null, Closure $endCallback = null)
    {
        $files = $plugin->getFiles();

        if (!is_dir($outputDirectory->getPathname()) && !mkdir($outputDirectory->getPathname(), 0777, true)) {
            throw new CreateDirectoryException($outputDirectory);
        }

        $zipName = implode(DIRECTORY_SEPARATOR, [
            $outputDirectory->getPathname(),
            $plugin->getName() . '_' . $plugin->getVersion() . '.zip',
        ]);

        if (is_file($zipName)) {
            $count = 10;
            while (!unlink($zipName) || is_file($zipName)) {
                if (--$count === 0) {
                    throw new RemovePackageException(new SplFileInfo($zipName));
                }
            }
        }

        $zip = new ZipArchive();
        $zipMessage = [];

        try {
            if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
                throw new CreatePackageException(new SplFileInfo($zipName));
            }

            if (!is_null($beginCallback) && is_callable($beginCallback)) {
                $beginCallback(iterator_count($files));
            }

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $localname = $plugin->getName() . DIRECTORY_SEPARATOR . sscanf($file->getPathname(), $plugin->getDirectory()->getPathname() . DIRECTORY_SEPARATOR . '%s')[0];

                if ($zip->addFile($file->getPathname(), $localname)) {
                    $zipMessage[] = sprintf('Added file to zip archive: %s', $localname);
                    if (!is_null($progressCallback) && is_callable($progressCallback)) {
                        $progressCallback();
                    }
                } else {
                    throw new AppendFileToPackageException(new SplFileInfo($zipName), $file);
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
