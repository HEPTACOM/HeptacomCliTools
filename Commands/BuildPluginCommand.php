<?php

namespace HeptacomCliTools\Commands;

use Exception;
use RecursiveCallbackFilterIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;
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
     * @var Plugin
     */
    protected $bootstrap;

    /**
     * @var array
     */
    protected $pluginInfo;

    protected function configure()
    {
        $this->setName('heptacom:plugin:build')
            ->setDescription('Builds a zip file for a plugin using the new plugin structure.')
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
        $this->zipPlugin();

        $this->output->writeln([
            'Plugin successfully built.',
            'Name: ' . $this->pluginDir->getBasename(),
            'Version: ' . $this->pluginInfo['version'],
        ]);
    }

    /**
     * @throws Exception
     */
    protected function preparePlugin()
    {
        $pluginName = $this->pluginDir->getBasename();
        if (!is_file($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . $pluginName . '.php')) {
            throw new Exception('No plugin bootstrap file was found in ' . $this->pluginDir);
        }
        if (!is_file($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml')) {
            throw new Exception('No plugin.xml info file was found in ' . $this->pluginDir);
        }

        /** @var XmlPluginInfoReader $pluginInfoReader */
        $pluginInfoReader = $this->getContainer()->get('shopware.plugin_xml_plugin_info_reader');
        $this->pluginInfo = $pluginInfoReader->read($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml');

        if (!array_key_exists('version', $this->pluginInfo)) {
            throw new Exception('No version was specified in plugin.xml file.');
        }
    }

    /**
     * @throws Exception
     */
    protected function lintPlugin()
    {
        $phpFiles = new RegexIterator($this->listFilesForZip(), "/^.+\.php$/i", RecursiveRegexIterator::GET_MATCH);
        
        foreach ($phpFiles as $phpFile) {
            if (!static::lintPHPFile($phpFile[0], $_)) {
                throw new Exception("Syntax error was detected in \"$phpFile[0]\"");
            }
        }

        $this->output->writeln("Plugin linted successfully.");
    }

    protected function zipPlugin()
    {
        $excludes = [
            '.DS_Store',
            '.idea',
            '.git',
        ];

        $command = 'cd ' . $this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . '..;';
        $command .= 'mkdir -p ' . $this->releaseDir->getPathname() . ';';
        $command .= 'rm -f ';
        $command .= $this->releaseDir->getPathname() . DIRECTORY_SEPARATOR;
        $command .= $this->pluginDir->getBasename() . '_' . $this->pluginInfo['version'] . '.zip;';
        $command .= '`which zip` -r ';
        $command .= $this->releaseDir->getPathname() . DIRECTORY_SEPARATOR;
        $command .= $this->pluginDir->getBasename() . '_' . $this->pluginInfo['version'] . '.zip';
        $command .= ' ' . $this->pluginDir->getBasename();
        foreach ($excludes as $exclude) {
            $command .= ' --exclude=*' . $exclude . '*';
        }
        $command .= ';';

        ob_start();
        system($command);
        $this->output->writeln(ob_get_clean());
    }

    /**
     * @return RecursiveIteratorIterator
     */
    private function listFilesForZip()
    {
        $files = new RecursiveDirectoryIterator($this->pluginDir->getPathname());
        $filesFilter = new RecursiveCallbackFilterIterator($files, function ($item, $key, $iterator) {
            // hide "hidden" files
            if (strncmp($item->getFilename(), '.', 1) === 0) {
                return false;
            }

            return true;
        });

        return new \RecursiveIteratorIterator($filesFilter);
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
