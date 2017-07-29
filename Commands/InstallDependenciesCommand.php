<?php

namespace HeptacomCliTools\Commands;

use HeptacomCliTools\Components\ComposerInstaller;
use HeptacomCliTools\Components\PluginData;
use SplFileInfo;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Commands\ShopwareCommand;

/**
 * Class InstallDependenciesCommand
 * @package HeptacomCliTools\Commands
 */
class InstallDependenciesCommand extends ShopwareCommand
{
    protected function configure()
    {
        $this->setName('ksk:plugin:dependencies')
            ->setDescription('Installs dependencies of the given plugin.')
            ->addArgument(
                'plugin',
                InputArgument::REQUIRED,
                'The name of the plugin you want to install dependencies for.'
            );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $pluginDirectory = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $input->getArgument('plugin')]);
        $plugin = new PluginData(new SplFileInfo($pluginDirectory));

        /** @var SplFileInfo $composerFile */
        foreach ($plugin->getComposerFiles() as $composerFile) {
            $output->writeln("Install composer dependencies {$composerFile->getPathname()} ...");
            if (ComposerInstaller::install($composerFile->getPathname(), $outputComposer)) {
                $output->writeln($outputComposer);
            } else {
                $output->writeln($outputComposer);
                $output->writeln(['', 'An error occured while installing dependencies.']);
                return;
            }
        }

        $output->writeln(['', 'All dependencies where installed.']);
    }
}
