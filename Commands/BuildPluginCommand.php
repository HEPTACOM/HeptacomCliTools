<?php

namespace HeptacomCliTools\Commands;

use Exception;
use SplFileInfo;
use Symfony\Component\ClassLoader\Psr4ClassLoader;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components\Plugin;
use Shopware\Components\Plugin\XmlPluginInfoReader;
use Shopware\Commands\ShopwareCommand;

class BuildPluginCommand extends ShopwareCommand
{
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

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginName = $input->getArgument('plugin');

        $this->releaseDir = new SplFileInfo(Shopware()->DocPath() .
            implode(DIRECTORY_SEPARATOR, ['HeptacomBuilds', 'plugins']));
        $this->pluginDir = new SplFileInfo(Shopware()->DocPath() .
            implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $pluginName]));

        $this->lintPlugin();
        $this->preparePlugin();
        $this->zipPlugin();

        $output->writeln([
        ]);
    }

    protected function preparePlugin()
    {
        $classLoader = new Psr4ClassLoader();
        $classLoader->register(true);

        $pluginName = $this->pluginDir->getBasename();
        if (!is_file($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . $pluginName . '.php')) {
            throw new Exception('No plugin bootstrap file was found in ' . $this->pluginDir);
        }
        if (!is_file($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml')) {
            throw new Exception('No plugin.xml info file was found in ' . $this->pluginDir);
        }

        $namespace = $pluginName;
        $className = '\\' . $namespace . '\\' .  $pluginName;

        $classLoader->addPrefix($namespace, $this->pluginDir->getPathname());

        $this->bootstrap = new $className(false);

        /** @var XmlPluginInfoReader $pluginInfoReader */
        $pluginInfoReader = $this->getContainer()->get('shopware.plugin_xml_plugin_info_reader');
        $this->pluginInfo = $pluginInfoReader->read($this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml');

        if (!array_key_exists('version', $this->pluginInfo)) {
            throw new Exception('No version was specified in plugin.xml file.');
        }
    }

    protected function lintPlugin()
    {
        $command = 'php -l ' . $this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . $this->pluginDir->getBasename() . '.php';
        $return_var = 0;

        ob_start();
        system($command, $return_var);
        ob_clean();

        if ($return_var) {
            throw new Exception('Syntax error was detected in ' .
                $this->pluginDir->getPathname() . DIRECTORY_SEPARATOR . $this->pluginDir->getBasename() . '.php');
        }
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

        system($command);
    }
}
