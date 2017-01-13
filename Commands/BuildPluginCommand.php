<?php

namespace HeptacomCliTools\Commands;

use Exception;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;
use ZipArchive;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\XmlPluginInfoReader;
use Shopware\Commands\ShopwareCommand;

class BuildPluginCommand extends ShopwareCommand
{
    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * @var SplFileInfo
     */
    protected $releaseDir;

    /**
     * @var SplFileInfo
     */
    protected $pluginDir;

    /**
     * @var string
     */
    protected $zipName;

    /**
     * @var array
     */
    protected $pluginInfo;

    protected function configure()
    {
        $this->setName('heptacom:plugin:build')
            ->setDescription('Builds a zip archive for a plugin using the new plugin structure.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The name of the plugin you want to build.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->output = $output;

        $pluginName = $input->getArgument('plugin');

        $this->releaseDir = new SplFileInfo(Shopware()->DocPath() .
            implode(DIRECTORY_SEPARATOR, ['HeptacomBuilds', 'plugins']));
        $this->pluginDir = new SplFileInfo(Shopware()->DocPath() .
            implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $pluginName]));

        $this->lintPlugin();
        $this->preparePlugin();
        $this->prepareZip();
        $this->createZip();

        $this->output->writeln([
            'Plugin built successfully.',
            'Name: ' . $this->pluginDir->getBasename(),
            'Version: ' . $this->pluginInfo['version'],
            'File: ' . $this->releaseDir . DIRECTORY_SEPARATOR . $this->pluginDir->getBasename() . '_' . $this->pluginInfo['version'] . '.zip',
        ]);
    }

    /**
     * @throws Exception
     */
    protected function preparePlugin()
    {
        $pluginName = $this->pluginDir->getBasename();
        if (!is_file($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . $pluginName . '.php')) {
            throw new Exception('No bootstrap file was found in ' . $this->pluginDir);
        }
        if (!is_file($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml')) {
            throw new Exception('No plugin.xml file was found in ' . $this->pluginDir);
        }

        /** @var XmlPluginInfoReader $pluginInfoReader */
        $pluginInfoReader = $this->getContainer()->get('shopware.plugin_xml_plugin_info_reader');
        $pluginInfoFile = $this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml';
        $this->pluginInfo = $pluginInfoReader->read($pluginInfoFile);

        if (!array_key_exists('version', $this->pluginInfo)) {
            throw new Exception(sprintf('No version was specified in %s', $pluginInfoFile));
        }
    }

    /**
     * @throws Exception
     */
    protected function lintPlugin()
    {
        $phpFiles = new RegexIterator($this->listFilesForZip(), "/^.+\.php$/i", RecursiveRegexIterator::GET_MATCH);
        
        foreach ($phpFiles as $phpFile) {
            if (!static::lintPHPFile($phpFile[0], $output)) {
                throw new Exception(
                    sprintf('Syntax error was detected in "%s". See the following error: %s %s', $phpFile[0], PHP_EOL, $output)
                );
            }
        }

        $this->output->writeln('Plugin linted successfully.');
    }

    protected function prepareZip()
    {
        if (!is_dir($this->releaseDir->getPathname()) && !mkdir($this->releaseDir->getPathname(), 0777, true)) {
            throw new Exception(sprintf('Could not create build folder at "%s"'), $this->releaseDir->getPathname());
        }

        $this->zipName = implode(DIRECTORY_SEPARATOR, [
            $this->releaseDir->getPathname(),
            $this->pluginDir->getBasename() . '_' . $this->pluginInfo['version'] . '.zip',
        ]);

        if (is_file($this->zipName) && !unlink($this->zipName)) {
            throw new Exception(sprintf(
                'Could not remove existing zip archive %s',
                $this->pluginDir->getBasename() . '_' . $this->pluginInfo['version'] . '.zip'
            ));
        }
    }

    protected function createZip()
    {
        $zip = new ZipArchive;

        if ($zip->open($this->zipName, ZipArchive::CREATE) !== true) {
            throw new Exception(sprintf('Could not create zip archive %s', $this->zipName));
        }

        $files = $this->listFilesForZip();
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            // $this->output->writeln($file->getFilename());
            $localname = $this->pluginDir->getBasename() . DIRECTORY_SEPARATOR . sscanf(
                $file->getPathname(),
                $this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . '%s'
            )[0];

            if ($zip->addFile($file->getPathname(), $localname)) {
                $this->output->writeln(sprintf('Added file to zip archive: %s', $localname));
            }
            else {
                throw new Exception(sprintf('Could not add file to zip archive: %s', $localname));
            }
        }

        $zip->close();
    }

    /**
     * @return RecursiveIteratorIterator
     */
    private function listFilesForZip()
    {
        $files = new RecursiveDirectoryIterator($this->pluginDir->getPathname());
        $filesFilter = new RecursiveCallbackFilterIterator($files, function (SplFileInfo $item, $key, $iterator) {
            // hide "hidden" files
            return (bool) (strncmp($item->getFilename(), '.', 1) !== 0);
        });

        return new RecursiveIteratorIterator($filesFilter);
    }

    /**
     * @param string
     * @param array
     * @return bool
     */
    private static function lintPHPFile($filename, &$output)
    {
        exec(sprintf('"%s" -l "%s"', PHP_BINARY, $filename), $output, $return_var);
        return !$return_var;
    }
}
