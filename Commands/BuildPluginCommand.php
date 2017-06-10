<?php

namespace HeptacomCliTools\Commands;

use HeptacomCliTools\Components\PluginBuilder;
use HeptacomCliTools\Components\PluginBuilder\Config;
use SplFileInfo;
use stdClass;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Components\Plugin\XmlPluginInfoReader;
use Shopware\Commands\ShopwareCommand;

/**
 * Class BuildPluginCommand
 * @package HeptacomCliTools\Commands
 */
class BuildPluginCommand extends ShopwareCommand
{
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
        $outputDirectory = Shopware()->DocPath() . implode(DIRECTORY_SEPARATOR, ['HeptacomBuilds', 'plugins']);
        $pluginName = $input->getArgument('plugin');
        $pluginDirectory = Shopware()->DocPath() .implode(DIRECTORY_SEPARATOR, ['custom', 'plugins', $pluginName]);

        /** @var stdClass $state */
        $state = new stdClass();
        $state->progressBar = null;

        /** @var Config $config */
        $config = Config::create()
            ->setLint(true)
            ->setPack(true)
            ->setName($pluginName)
            ->setPluginDirectory(new SplFileInfo($pluginDirectory))
            ->setOutputDirectory(new SplFileInfo($outputDirectory))
            ->setBlacklist([
                'node_modules'
            ])
            ->setWhitelist([
                '.swNoEncryption'
            ])
            ->setPackBeginCallback(function ($count) use($output, $state) {
                $output->writeln('Creating zip archive...');
                $state->progressBar = new ProgressBar($output, $count);
            })
            ->setPackProgressCallback(function () use ($state) {
                $state->progressBar->advance();
            })
            ->setPackEndCallback(function ($message) use ($output, $state) {
                $state->progressBar->finish();
                $output->writeln(array_merge([''], $message));
            })
            ->setLintBeginCallback(function ($count) use($output, $state) {
                $output->writeln('Linting plugin...');
                $state->progressBar = new ProgressBar($output, $count);
            })
            ->setLintProgressCallback(function () use ($state) {
                $state->progressBar->advance();
            })
            ->setLintEndCallback(function () use ($output, $state) {
                $state->progressBar->finish();
                $output->writeln(['', 'All PHP files linted successfully.']);
            });

        /** @var XmlPluginInfoReader $pluginInfoReader */
        $pluginInfoReader = $this->getContainer()->get('shopware.plugin_xml_plugin_info_reader');
        $pluginInfoFile = $config->getPluginDirectory()->getPathname() . DIRECTORY_SEPARATOR . 'plugin.xml';
        $pluginInfo = $pluginInfoReader->read($pluginInfoFile);

        $config->setVersion($pluginInfo['version']);

        PluginBuilder::build($config);

        $output->writeln([
            'Plugin built successfully.',
            'Location: ' . $config->getOutputDirectory()->getPathname() . DIRECTORY_SEPARATOR . $config->getPluginDirectory()->getBasename() . '_' . $config->getVersion() . '.zip',
        ]);
    }
}
